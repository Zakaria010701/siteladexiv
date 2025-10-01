<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\DatabaseNotification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\PersonalAccessToken;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Concerns\HasEmailAddresses;
use App\Models\Concerns\HasPhoneNumbers;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Color\Rgb;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $current_branch_id
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $street
 * @property string|null $postcode
 * @property string|null $location
 * @property string|null $phone_number
 * @property string|null $birthday
 * @property int $user_work_type_id
 * @property-read Collection<int, Todo> $assignedTodos
 * @property-read int|null $assigned_todos_count
 * @property-read Collection<int, Branch> $branches
 * @property-read int|null $branches_count
 * @property-read Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @property-read Collection<int, Category> $consultationCategories
 * @property-read int|null $consultation_categories_count
 * @property-read Branch|null $currentBranch
 * @property-read Collection<int, Todo> $customerTodos
 * @property-read int|null $customer_todos_count
 * @property-read Collection<int, EmailAddress> $emailAddresses
 * @property-read int|null $email_addresses_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection<int, PhoneNumber> $phoneNumbers
 * @property-read int|null $phone_numbers_count
 * @property-read Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @property-read Collection<int, Service> $services
 * @property-read int|null $services_count
 * @property-read Collection<int, TimeReportOverview> $timeReportOverviews
 * @property-read int|null $time_report_overviews_count
 * @property-read Collection<int, TimeReport> $timeReports
 * @property-read int|null $time_reports_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read UserDetail|null $userDetails
 * @property-read UserWorkType $userWorkType
 * @property-read Collection<int, WorkTime> $workTimes
 * @property-read int|null $work_times_count
 *
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User onlyTrashed()
 * @method static Builder|User permission($permissions, $without = false)
 * @method static Builder|User query()
 * @method static Builder|User role($roles, $guard = null, $without = false)
 * @method static Builder|User whereBirthday($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereCurrentBranchId($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereFirstname($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLastname($value)
 * @method static Builder|User whereLocation($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePhoneNumber($value)
 * @method static Builder|User wherePostcode($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereStreet($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUserWorkTypeId($value)
 * @method static Builder|User withTrashed()
 * @method static Builder|User withoutPermission($permissions)
 * @method static Builder|User withoutRole($roles, $guard = null)
 * @method static Builder|User withoutTrashed()
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasEmailAddresses, HasPhoneNumbers;
    use HasEmailAddresses, HasPhoneNumbers;
    use CausesActivity;
    use HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_provider' => 'boolean',
        'show_in_frontend' => 'boolean',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => trim(($attributes['firstname'] ?? $attributes['name']).' '.($attributes['lastname'] ?? '')),
        );
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return null;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->getFilamentAvatarUrl();
    }

    public function getUserName(): ?string
    {
        return $this->full_name;
    }

    /*
    |--------------------------------------------------
    | Relations
    |--------------------------------------------------
    */

    public function assignedTodos(): HasMany
    {
        return $this->hasMany(Todo::class, 'assigned_to');
    }

    public function availabilities(): MorphMany
    {
        return $this->morphMany(Availability::class, 'planable');
    }

    public function currentAvailability(): MorphOne
    {
        return $this->morphOne(Availability::class, 'planable')->ofMany([
            'start_date' => 'max',
            'created_at' => 'max'
        ], function (Builder $query) {
            $query->where('start_date', '<', now())
                ->where(fn (Builder $query) => $query
                    ->where('end_date', '>=', now())
                    ->orWhereNull('end_date'));
        });
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_user');
    }

    public function consultationCategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'consultation_category_user', 'user_id', 'category_id');
    }

    public function currentBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'current_branch_id');
    }

    public function customerTodos(): HasMany
    {
        return $this->hasMany(Todo::class, 'customer_id');
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function processedLeaves(): HasMany
    {
        return $this->hasMany(Leave::class, 'processed_by_id');
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function preferedByCustomers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class)->withTimestamps();
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    public function systemResources(): MorphToMany
    {
        return $this->morphToMany(SystemResource::class, 'dependable', 'system_resource_dependables');
    }

    public function timeReports(): HasMany
    {
        return $this->hasMany(TimeReport::class);
    }

    public function timePlans(): HasMany
    {
        return $this->hasMany(TimePlan::class);
    }

    public function timeReportOverviews(): HasMany
    {
        return $this->hasMany(TimeReportOverview::class);
    }

    public function userDetails(): HasOne
    {
        return $this->hasOne(UserDetail::class);
    }

    public function userWorkType(): BelongsTo
    {
        return $this->belongsTo(UserWorkType::class);
    }

    public function workTimes(): HasMany
    {
        return $this->hasMany(WorkTime::class);
    }

    public function scopeProvider(Builder $query)
    {
        $query->where('is_provider', true);
    }

    public function scopeShowInFrontend(Builder $query)
    {
        $query->where('show_in_frontend', true);
    }
}
