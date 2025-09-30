<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use App\Enums\TimeRecords\LeaveType;
use App\Observers\LeaveObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $user_id
 * @property LeaveType $leave_type
 * @property Carbon $from
 * @property Carbon $till
 * @property int $processed_by_id
 * @property Carbon|null $approved_at
 * @property Carbon|null $denied_at
 * @property string|null $user_note
 * @property string|null $admin_note
 * @property array|null $meta
 * @property-read bool $is_approved
 * @property-read bool $is_denied
 * @property-read User $processedBy
 * @property-read User $user
 *
 * @method static Builder|Leave approved()
 * @method static Builder|Leave denied()
 * @method static Builder|Leave newModelQuery()
 * @method static Builder|Leave newQuery()
 * @method static Builder|Leave notDenied()
 * @method static Builder|Leave onlyTrashed()
 * @method static Builder|Leave query()
 * @method static Builder|Leave unapproved()
 * @method static Builder|Leave whereAdminNote($value)
 * @method static Builder|Leave whereApprovedAt($value)
 * @method static Builder|Leave whereCreatedAt($value)
 * @method static Builder|Leave whereDeletedAt($value)
 * @method static Builder|Leave whereDeniedAt($value)
 * @method static Builder|Leave whereFrom($value)
 * @method static Builder|Leave whereId($value)
 * @method static Builder|Leave whereLeaveType($value)
 * @method static Builder|Leave whereMeta($value)
 * @method static Builder|Leave whereProcessedById($value)
 * @method static Builder|Leave whereTill($value)
 * @method static Builder|Leave whereUpdatedAt($value)
 * @method static Builder|Leave whereUserId($value)
 * @method static Builder|Leave whereUserNote($value)
 * @method static Builder|Leave withTrashed()
 * @method static Builder|Leave withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy([LeaveObserver::class])]
class Leave extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'from' => 'date',
        'till' => 'date',
        'leave_type' => LeaveType::class,
        'approved_at' => 'datetime',
        'denied_at' => 'datetime',
        'meta' => 'array',
    ];

    public function isApproved(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => ! is_null($attributes['approved_at']),
        );
    }

    public function isDenied(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => ! is_null($attributes['denied_at']),
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_id');
    }

    public function availabilityAbsence(): BelongsTo
    {
        return $this->belongsTo(AvailabilityAbsence::class);
    }

    public function scopeApproved(Builder $query): void
    {
        $query->whereNotNull('approved_at');
    }

    public function scopeUnapproved(Builder $query): void
    {
        $query->whereNull('approved_at');
    }

    public function scopeDenied(Builder $query): void
    {
        $query->whereNotNull('denied_at');
    }

    public function scopeNotDenied(Builder $query): void
    {
        $query->whereNull('denied_at');
    }
}
