<?php

namespace App\Models;

use App\Enums\Customers\ContactMethod;
use App\Enums\Customers\CustomerOption;
use App\Enums\Gender;
use App\Models\Concerns\CanBeVerified;
use App\Models\Concerns\HasEmailAddresses;
use App\Models\Concerns\HasPhoneNumbers;
use Database\Factories\CustomerFactory;
use Eloquent;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Color\Rgb;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Service;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $title
 * @property Gender $gender
 * @property string|null $name
 * @property string $firstname
 * @property string $lastname
 * @property string|null $email
 * @property string|null $phone_number
 * @property mixed|null $password
 * @property string|null $remember_token
 * @property array|null $options
 * @property int|null $parent_id
 * @property-read Collection<int, Appointment> $appointments
 * @property-read int|null $appointments_count
 * @property-read mixed $contact_email
 * @property-read Collection<int, CustomerContact> $customerContacts
 * @property-read int|null $customer_contacts_count
 * @property-read Collection<int, CustomerDiscount> $customerDiscounts
 * @property-read int|null $customer_discounts_count
 * @property-read Collection<int, EmailAddress> $emailAddresses
 * @property-read int|null $email_addresses_count
 * @property-read mixed $full_name
 * @property-read Collection<int, Note> $notes
 * @property-read int|null $notes_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Customer|null $parent
 * @property-read Collection<int, Payment> $payments
 * @property-read int|null $payments_count
 * @property-read Collection<int, PhoneNumber> $phoneNumbers
 * @property-read int|null $phone_numbers_count
 * @property-read mixed $primary_email
 * @property-read Collection<int, ServiceCredit> $serviceCredits
 * @property-read int|null $service_credits_count
 *
 * @method static CustomerFactory factory($count = null, $state = [])
 * @method static Builder|Customer newModelQuery()
 * @method static Builder|Customer newQuery()
 * @method static Builder|Customer onlyTrashed()
 * @method static Builder|Customer query()
 * @method static Builder|Customer whereCreatedAt($value)
 * @method static Builder|Customer whereDeletedAt($value)
 * @method static Builder|Customer whereEmail($value)
 * @method static Builder|Customer whereFirstname($value)
 * @method static Builder|Customer whereGender($value)
 * @method static Builder|Customer whereId($value)
 * @method static Builder|Customer whereLastname($value)
 * @method static Builder|Customer whereName($value)
 * @method static Builder|Customer whereOptions($value)
 * @method static Builder|Customer whereParentId($value)
 * @method static Builder|Customer wherePassword($value)
 * @method static Builder|Customer wherePhoneNumber($value)
 * @method static Builder|Customer whereRememberToken($value)
 * @method static Builder|Customer whereTitle($value)
 * @method static Builder|Customer whereUpdatedAt($value)
 * @method static Builder|Customer withTrashed()
 * @method static Builder|Customer withoutTrashed()
 *
 * @mixin Eloquent
 */
class Customer extends Authenticatable implements HasMedia
{
    use HasApiTokens;
    use HasEmailAddresses, HasPhoneNumbers;
    use HasFactory;
    use CanBeVerified;
    use InteractsWithMedia;
    use Notifiable;
    use CausesActivity;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'gender',
        'firstname',
        'lastname',
        'name',
        'email',
        'phone_number',
        'birthday',
        'prefered_contact_method',
        'options',
        'parent_id',
        'meta',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'gender' => Gender::class,
        'prefered_contact_method' => ContactMethod::class,
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'options' => 'array',
        'meta' => 'array',
    ];

    /*------------------------------
    |   Attributes
    --------------------------------*/

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => sprintf('%s %s %s', $attributes['title'], $attributes['firstname'], $attributes['lastname']),
        );
    }

    public function label(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => sprintf('%s %s %s (%s)', $attributes['title'], $attributes['firstname'], $attributes['lastname'], formatDate($attributes['birthday'])),
        );
    }

    public function services()
    {
        return $this->belongsToMany(
            Service::class,
            'service_user',
            'customer_id',   // passt jetzt zur Spalte in Pivot
            'service_id'
        );
    }



    public function contactEmail(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $contactMail = $this->emailAddresses->where('is_contact', true)->first()?->email ?? null;
                if (is_null($contactMail)) {
                    $contactMail = $this->email ?? $this->emailAddresses->first()?->email;
                }

                return $contactMail;
            },
        );
    }

    public function primaryEmail(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if (isset($this->email)) {
                    return $this->email;
                }

                if ($this->emailAddresses->isNotEmpty()) {
                    return $this->emailAddresses->first()->email;
                }

                if (isset($this->parent)) {
                    return $this->parent->primary_email;
                }

                return null;
            },
        );
    }

    public function primaryPhoneNumber(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if (isset($this->phone_number)) {
                    return $this->phone_number;
                }

                if ($this->phoneNumbers->isNotEmpty()) {
                    return $this->phoneNumbers->first()->phone_number;
                }

                if (isset($this->parent)) {
                    return $this->parent->phone_number;
                }

                return null;
            },
        );
    }

    /*------------------------------
    |   Scopes
    --------------------------------*/

    /*------------------------------
    |   Relationships
    --------------------------------*/

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Customer::class, 'parent_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function customPackages(): HasMany
    {
        return $this->hasMany(ServicePackage::class, 'customer_id');
    }

    public function customerContacts(): HasMany
    {
        return $this->hasMany(CustomerContact::class);
    }

    public function customerCredits(): HasMany
    {
        return $this->hasMany(CustomerCredit::class);
    }

    public function customerDiscounts(): HasMany
    {
        return $this->hasMany(CustomerDiscount::class);
    }

    public function invoices(): MorphMany
    {
        return $this->morphMany(Invoice::class, 'recipient');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function serviceCredits(): HasMany
    {
        return $this->hasMany(ServiceCredit::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'parent_id');
    }

    public function preferedProviders(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function transactions(): HasManyThrough
    {
        return $this->through('accounts')->has('transactions');
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function waitingListEntries(): HasMany
    {
        return $this->hasMany(WaitingListEntry::class);
    }

    /*------------------------------
    |   Methods
    --------------------------------*/

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'email';
    }

    public function getAvatarUrl(): ?string
    {
        $name = str($this->firstname . ' ' . $this->lastname)
            ->trim()
            ->explode(' ')
            ->map(fn(string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');

        $backgroundColor = Rgb::fromString('rgb(' . FilamentColor::getColors()['gray'][950] . ')')->toHex();

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=FFFFFF&background=' . str($backgroundColor)->after('#');
    }

    public function getUserName(): ?string
    {
        return $this->full_name;
    }

    public function toggleOption(string|CustomerOption $option): static
    {
        if ($option instanceof CustomerOption) {
            $option = $option->value;
        }
        if ($this->hasOption($option)) {
            unset($this->options[array_search($option, $this->options)]);
        } else {
            $this->options[] = $option;
        }
        $this->save();

        return $this;
    }

    public function hasOption(string|CustomerOption $option): bool
    {
        if (is_null($this->options)) {
            return false;
        }

        if ($option instanceof CustomerOption) {
            $option = $option->value;
        }

        return in_array($option, $this->options);
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return array<string, string>|string
     */
    public function routeNotificationForMail(Notification $notification): array|string
    {
        return [$this->contact_email => $this->lastname];
    }

    public function routeForSms77()
    {
        return $this->phone_number;
    }

    public function routeNotificationForSms77($notification)
    {
        return $this->phone_number;
    }

    /**
     * Set the name field based on firstname and lastname
     */
    public function updateNameField(): void
    {
        $this->name = trim(sprintf('%s %s', $this->firstname, $this->lastname));
        $this->save();
    }

    /**
     * Get the full name (firstname + lastname)
     */
    public function getFullNameAttribute(): string
    {
        return trim(sprintf('%s %s', $this->firstname, $this->lastname));
    }
}
