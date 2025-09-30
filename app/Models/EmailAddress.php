<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $addressable_type
 * @property int $addressable_id
 * @property string $email
 * @property int $is_contact
 * @property-read Model|\Eloquent $addressable
 * @property-read Customer|null $customer
 *
 * @method static Builder|EmailAddress newModelQuery()
 * @method static Builder|EmailAddress newQuery()
 * @method static Builder|EmailAddress query()
 * @method static Builder|EmailAddress whereAddressableId($value)
 * @method static Builder|EmailAddress whereAddressableType($value)
 * @method static Builder|EmailAddress whereCreatedAt($value)
 * @method static Builder|EmailAddress whereEmail($value)
 * @method static Builder|EmailAddress whereId($value)
 * @method static Builder|EmailAddress whereIsContact($value)
 * @method static Builder|EmailAddress whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EmailAddress extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function addressable(): MorphTo
    {
        return $this->morphTo('addressable');
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'email', 'email');
    }
}
