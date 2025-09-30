<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $callable_type
 * @property int $callable_id
 * @property string $phone_number
 * @property int $is_contact
 * @property-read Model|\Eloquent $callable
 *
 * @method static Builder|PhoneNumber newModelQuery()
 * @method static Builder|PhoneNumber newQuery()
 * @method static Builder|PhoneNumber query()
 * @method static Builder|PhoneNumber whereCallableId($value)
 * @method static Builder|PhoneNumber whereCallableType($value)
 * @method static Builder|PhoneNumber whereCreatedAt($value)
 * @method static Builder|PhoneNumber whereId($value)
 * @method static Builder|PhoneNumber whereIsContact($value)
 * @method static Builder|PhoneNumber wherePhoneNumber($value)
 * @method static Builder|PhoneNumber whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PhoneNumber extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function callable(): MorphTo
    {
        return $this->morphTo('callable');
    }
}
