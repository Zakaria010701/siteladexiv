<?php

namespace App\Models;

use App\Enums\Appointments\AppointmentOrderStatus;
use App\Enums\Appointments\AppointmentStatus;
use App\Enums\Appointments\AppointmentType;
use App\Enums\Appointments\CancelReason;
use App\Enums\TimeStep;
use App\Events\Appointments\AppointmentCheckedInEvent;
use App\Events\Appointments\AppointmentCheckedOutEvent;
use App\Events\Appointments\AppointmentConfirmedEvent;
use App\Events\Appointments\AppointmentControlledEvent;
use App\Models\Concerns\Appointments\HasComplaint;
use App\Models\Concerns\Appointments\HasStatus;
use App\Models\Concerns\CanBeVerified;
use App\Observers\AppointmentObserver;
use Database\Factories\AppointmentFactory;
use Eloquent;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;



/**
 *
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property Carbon|null $approved_at
 * @property Carbon|null $canceled_at
 * @property Carbon|null $done_at
 * @property Carbon|null $reminder_sent_at
 * @property int $branch_id
 * @property int $room_id
 * @property int|null $customer_id
 * @property int|null $user_id
 * @property int|null $category_id
 * @property AppointmentType $type
 * @property AppointmentStatus $status
 * @property Carbon $start
 * @property Carbon $end
 * @property int|null $done_by_id
 * @property int|null $next_appointment_in
 * @property TimeStep|null $next_appointment_step
 * @property Carbon|null $next_appointment_date
 * @property Carbon|null $next_appointment_reminder_sent_at
 * @property CancelReason|null $cancel_reason
 * @property string|null $description
 * @property int $difficulty_score
 * @property Carbon|null $check_in_at
 * @property Carbon|null $check_out_at
 * @property Carbon|null $controlled_at
 * @property Carbon|null $confirmed_at
 * @property int|null $treatment_type_id
 * @property array<array-key, mixed>|null $meta
 * @property string|null $google_event_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Activity> $activityLog
 * @property-read int|null $activity_log_count
 * @property-read AppointmentConsultation|null $appointmentConsultation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CustomerCredit> $appointmentCredits
 * @property-read int|null $appointment_credits_count
 * @property-read AppointmentDetail|null $appointmentDetail
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AppointmentItem> $appointmentItems
 * @property-read int|null $appointment_items_count
 * @property-read AppointmentOrder|null $appointmentOrder
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AppointmentServiceDetail> $appointmentServiceDetails
 * @property-read int|null $appointment_service_details_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Appointment> $appointments
 * @property-read int|null $appointments_count
 * @property-read mixed $arrival_time
 * @property-read Branch $branch
 * @property-read Category|null $category
 * @property-read mixed $checked_in
 * @property-read mixed $checked_out
 * @property-read AppointmentComplaint|null $complaint
 * @property-read mixed $confirmed
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Contract> $contracts
 * @property-read int|null $contracts_count
 * @property-read mixed $controlled
 * @property-read Verification|null $currentVerification
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ServicePackage> $customPackages
 * @property-read int|null $custom_packages_count
 * @property-read Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Activity> $customerActivityLog
 * @property-read int|null $customer_activity_log_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Contract> $customerContracts
 * @property-read int|null $customer_contracts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CustomerCredit> $customerCredits
 * @property-read int|null $customer_credits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Invoice> $customerInvoices
 * @property-read int|null $customer_invoices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Note> $customerNotes
 * @property-read int|null $customer_notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Voucher> $customerVouchers
 * @property-read int|null $customer_vouchers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Discount> $discounts
 * @property-read int|null $discounts_count
 * @property-read User|null $doneBy
 * @property-read mixed $duration
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Invoice> $invoices
 * @property-read int|null $invoices_count
 * @property-read Activity|null $lastActivity
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Note> $notes
 * @property-read int|null $notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Customer> $participants
 * @property-read int|null $participants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Payment> $payments
 * @property-read int|null $payments_count
 * @property-read Room $room
 * @property-read mixed $search_title
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ServicePackage> $servicePackages
 * @property-read int|null $service_packages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SystemResource> $systemResources
 * @property-read int|null $system_resources_count
 * @property-read mixed $title
 * @property-read TreatmentType|null $treatmentType
 * @property-read User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Verification> $verifications
 * @property-read int|null $verifications_count
 * @method static Builder<static>|Appointment checkedIn()
 * @method static Builder<static>|Appointment checkedOut()
 * @method static Builder<static>|Appointment confirmed()
 * @method static Builder<static>|Appointment controlled()
 * @method static AppointmentFactory factory($count = null, $state = [])
 * @method static Builder<static>|Appointment newModelQuery()
 * @method static Builder<static>|Appointment newQuery()
 * @method static Builder<static>|Appointment notCanceled()
 * @method static Builder<static>|Appointment notCheckedIn()
 * @method static Builder<static>|Appointment notCheckedOut()
 * @method static Builder<static>|Appointment notConfirmed()
 * @method static Builder<static>|Appointment notControlled()
 * @method static Builder<static>|Appointment notVerified()
 * @method static Builder<static>|Appointment onlyTrashed()
 * @method static Builder<static>|Appointment open()
 * @method static Builder<static>|Appointment orderStatus(AppointmentOrderStatus $status, string $operator = '=')
 * @method static Builder<static>|Appointment paid()
 * @method static Builder<static>|Appointment query()
 * @method static Builder<static>|Appointment status(AppointmentStatus $status, string $operator = '=')
 * @method static Builder<static>|Appointment verified()
 * @method static Builder<static>|Appointment whereApprovedAt($value)
 * @method static Builder<static>|Appointment whereBranchId($value)
 * @method static Builder<static>|Appointment whereCancelReason($value)
 * @method static Builder<static>|Appointment whereCanceledAt($value)
 * @method static Builder<static>|Appointment whereCategoryId($value)
 * @method static Builder<static>|Appointment whereCheckInAt($value)
 * @method static Builder<static>|Appointment whereCheckOutAt($value)
 * @method static Builder<static>|Appointment whereConfirmedAt($value)
 * @method static Builder<static>|Appointment whereControlledAt($value)
 * @method static Builder<static>|Appointment whereCreatedAt($value)
 * @method static Builder<static>|Appointment whereCustomerId($value)
 * @method static Builder<static>|Appointment whereDeletedAt($value)
 * @method static Builder<static>|Appointment whereDescription($value)
 * @method static Builder<static>|Appointment whereDifficultyScore($value)
 * @method static Builder<static>|Appointment whereDoneAt($value)
 * @method static Builder<static>|Appointment whereDoneById($value)
 * @method static Builder<static>|Appointment whereEnd($value)
 * @method static Builder<static>|Appointment whereGoogleEventId($value)
 * @method static Builder<static>|Appointment whereId($value)
 * @method static Builder<static>|Appointment whereMeta($value)
 * @method static Builder<static>|Appointment whereNextAppointmentDate($value)
 * @method static Builder<static>|Appointment whereNextAppointmentIn($value)
 * @method static Builder<static>|Appointment whereNextAppointmentReminderSentAt($value)
 * @method static Builder<static>|Appointment whereNextAppointmentStep($value)
 * @method static Builder<static>|Appointment whereReminderSentAt($value)
 * @method static Builder<static>|Appointment whereRoomId($value)
 * @method static Builder<static>|Appointment whereStart($value)
 * @method static Builder<static>|Appointment whereStatus($value)
 * @method static Builder<static>|Appointment whereTreatmentTypeId($value)
 * @method static Builder<static>|Appointment whereType($value)
 * @method static Builder<static>|Appointment whereUpdatedAt($value)
 * @method static Builder<static>|Appointment whereUserId($value)
 * @method static Builder<static>|Appointment withTrashed()
 * @method static Builder<static>|Appointment withoutTrashed()
 * @mixin Eloquent
 */
#[ObservedBy(AppointmentObserver::class)]
class Appointment extends Model implements HasMedia
{
    use HasFactory;
    use HasStatus;
    use HasComplaint;
    use InteractsWithMedia;
    use CanBeVerified;
    use LogsActivity;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => AppointmentType::class,
        'status' => AppointmentStatus::class,
        'start' => 'datetime',
        'end' => 'datetime',
        'canceled_at' => 'datetime',
        'done_at' => 'datetime',
        'approved_at' => 'datetime',
        'next_appointment_step' => TimeStep::class,
        'next_appointment_date' => 'datetime',
        'next_appointment_reminder_sent_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'controlled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'cancel_reason' => CancelReason::class,
        'meta' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'status',
                'type',
                'category.name',
                'user.name',
                'room.name',
                'branch.name',
                'doneBy.name',
                'appointmentOrder.status',
                'start',
                'end',
                'description',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /*------------------------------
    |   Attributes
    --------------------------------*/

    public function duration(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return Carbon::parse($attributes['start'])->diffInMinutes($attributes['end']);
            }
        );
    }

    public function title(): Attribute
    {
        return Attribute::make(
            get: function () {
                return sprintf('%s %s %s',
                    formatDateTime($this->start),
                    $this->type->getShortCode(),
                    $this->category?->short_code ?? ''
                );
            }
        );
    }

    public function searchTitle(): Attribute
    {
        return Attribute::make(
            get: function () {
                return sprintf('%s %s %s %s',
                    formatDateTime($this->start),
                    $this->type->getShortCode(),
                    $this->category?->short_code ?? '',
                    $this->customer?->full_name ?? ''
                );
            }
        );
    }

    public function checkedIn(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => isset($attributes['check_in_at']),
        );
    }

    public function checkedOut(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => isset($attributes['check_out_at']),
        );
    }

    public function controlled(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => isset($attributes['controlled_at']),
        );
    }

    public function confirmed(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => isset($attributes['confirmed_at']),
        );
    }

    public function arrivalTime(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => Carbon::parse($attributes['start'])->subMinutes($attributes['customer_should_arrive_prior_to_appointment_minutes']),
        );
    }

    /*------------------------------
    |   Scopes
    --------------------------------*/

    public function scopeCheckedIn(Builder $query): void
    {
        $query->whereNotNull('checked_in_at');
    }

    public function scopeNotCheckedIn(Builder $query): void
    {
        $query->whereNull('checked_in_at');
    }

    public function scopeCheckedOut(Builder $query): void
    {
        $query->whereNotNull('checked_out_at');
    }

    public function scopeNotCheckedOut(Builder $query): void
    {
        $query->whereNull('checked_out_at');
    }

    public function scopeControlled(Builder $query): void
    {
        $query->whereNotNull('controlled_at');
    }

    public function scopeNotControlled(Builder $query): void
    {
        $query->whereNull('controlled_at');
    }

    public function scopeConfirmed(Builder $query): void
    {
        $query->whereNotNull('confirmed_at');
    }

    public function scopeNotConfirmed(Builder $query): void
    {
        $query->whereNull('confirmed_at');
    }

    public function scopeOrderStatus(Builder $query, AppointmentOrderStatus $status, string $operator = '='): void
    {
        $query->whereHas('appointmentOrder', fn (AppointmentOrder|Builder $query) => $query->status($status, $operator));
    }

    public function scopePaid(Appointment|Builder $query): void
    {
        $query->orderStatus(AppointmentOrderStatus::Paid);
    }

    public function scopeOpen(Appointment|Builder $query): void
    {
        $query->orderStatus(AppointmentOrderStatus::Open);
    }

    /*------------------------------
    |   Relationships
    --------------------------------*/

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'customer_id', 'customer_id');
    }

    public function appointmentConsultation(): HasOne
    {
        return $this->hasOne(AppointmentConsultation::class);
    }

    public function appointmentItems(): HasMany
    {
        return $this->hasMany(AppointmentItem::class);
    }

    public function appointmentDetail(): HasOne
    {
        return $this->hasOne(AppointmentDetail::class);
    }

    public function appointmentOrder(): HasOne
    {
        return $this->hasOne(AppointmentOrder::class);
    }

    public function appointmentServiceDetails(): HasMany
    {
        return $this->hasMany(AppointmentServiceDetail::class);
    }

    public function systemResources(): BelongsToMany
    {
        return $this->belongsToMany(SystemResource::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function appointmentCredits(): MorphMany
    {
        return $this->morphMany(CustomerCredit::class, 'source');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerContracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'customer_id', 'customer_id');
    }

    public function customPackages(): HasMany
    {
        return $this->hasMany(ServicePackage::class, 'customer_id', 'customer_id');
    }

    public function customerCredits(): HasMany
    {
        return $this->hasMany(CustomerCredit::class, 'customer_id', 'customer_id');
    }

    public function customerInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'recipient_id', 'customer_id')->where('recipient_type', Customer::class);
    }

    public function customerVouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'customer_id', 'customer_id');
    }

    public function customerActivityLog(): HasMany
    {
        return $this->hasMany(Activity::class, 'customer_id', 'customer_id');
    }

    public function activityLog(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function lastActivity(): MorphOne
    {
        return $this->morphOne(Activity::class, 'subject')->latestOfMany();
    }

    public function customerNotes(): HasMany
    {
        return $this->hasMany(Note::class, 'customer_id', 'customer_id');
    }

    public function discounts(): MorphMany
    {
        return $this->morphMany(Discount::class, 'discountable');
    }

    public function doneBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoices(): MorphMany
    {
        return $this->morphMany(Invoice::class, 'source');
    }

    public function media(): MorphMany
    {
        return $this->customer->media();
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'appointment_customer');
    }

    public function services()
{
    return $this->belongsToMany(Service::class, 'appointment_service_details', 'appointment_id', 'service_id');
}


    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function servicePackages(): BelongsToMany
    {
        return $this->belongsToMany(ServicePackage::class)->withTimestamps();
    }

    public function treatmentType(): BelongsTo
    {
        return $this->belongsTo(TreatmentType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*------------------------------
    |   Methods
    --------------------------------*/

    public function isPaid(): bool
    {
        if (is_null($this->appointmentOrder)) {
            return false;
        }

        return $this->appointmentOrder->status->isPaid();
    }

    public function getColor(): string|array|null
    {
        if ($this->type->getOverrideColor()) {
            return $this->type->getColor();
        }

        if($this->status->isDone()) {
            if($this->appointmentOrder?->status->isOpen()) {
                return Color::Red;
            }
        }

        if (isset($this->category)) {
            return Color::hex($this->category->color);
        }

        return $this->type->getColor();
    }

    public function markCheckedIn(bool $sendNotification = true): self
    {
        $old = clone $this;
        $this->check_in_at = now();
        $this->save();

        AppointmentCheckedInEvent::dispatch($this, auth()->user(), $sendNotification);

        return $this;
    }

    public function markCheckedOut(bool $sendNotification = true): self
    {
        $old = clone $this;
        $this->check_out_at = now();
        $this->save();

        AppointmentCheckedOutEvent::dispatch($this, auth()->user(), $sendNotification);

        return $this;
    }

    public function markControlled(bool $sendNotification = true): self
    {
        $old = clone $this;
        $this->controlled_at = now();
        $this->save();

        AppointmentControlledEvent::dispatch($this, auth()->user());

        return $this;
    }

    public function markConfirmed(bool $sendNotification = true): self
    {
        $old = clone $this;
        $this->confirmed_at = now();
        $this->save();

        AppointmentConfirmedEvent::dispatch($this, auth()->user());

        return $this;
    }

    /*public function getServicesPackages(): Collection
    {
        $services = $this->getServices()->pluck('id')->toArray();

        return ServicePackage::query()
            ->withCount([
                'services as needed_services' => fn (Builder $query) => $query->whereIn('service_id', $services),
                'services as other_services' => fn (Builder $query) => $query->whereNotIn('service_id', $services),
            ])
            ->where('category_id', $this->category_id)
            ->where(fn (Builder $query) => $query
                ->whereNull('customer_id')
                ->orWhere('customer_id', $this->customer_id)
            )
            ->having('needed_services', '>', 0)
            ->having('other_services', '=', 0)
            ->get();
    }*/

    public function getServices(): Collection
    {
        return $this->appointmentItems()
            ->with(['purchasable'])
            ->where('purchasable_type', Service::class)
            ->get()
            ->pluck('purchasable');
    }
}
