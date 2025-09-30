<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Observers\PayrollObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $user_id
 * @property int|null $time_report_id
 * @property int|null $previous_id
 * @property Carbon $from
 * @property Carbon $till
 * @property int $minutes
 * @property string $hourly_wage
 * @property string $payment
 * @property string $extra_payment
 * @property string $prev_balance
 * @property string $payout
 * @property string $current_balance
 * @property array $meta
 * @property-read Payroll|null $next
 * @property-read Payroll|null $previous
 * @property-read TimeReport|null $timeReport
 * @property-read User $user
 *
 * @method static Builder|Payroll newModelQuery()
 * @method static Builder|Payroll newQuery()
 * @method static Builder|Payroll onlyTrashed()
 * @method static Builder|Payroll query()
 * @method static Builder|Payroll whereCreatedAt($value)
 * @method static Builder|Payroll whereCurrentBalance($value)
 * @method static Builder|Payroll whereDeletedAt($value)
 * @method static Builder|Payroll whereExtraPayment($value)
 * @method static Builder|Payroll whereFrom($value)
 * @method static Builder|Payroll whereHourlyWage($value)
 * @method static Builder|Payroll whereId($value)
 * @method static Builder|Payroll whereMeta($value)
 * @method static Builder|Payroll whereMinutes($value)
 * @method static Builder|Payroll wherePayment($value)
 * @method static Builder|Payroll wherePayout($value)
 * @method static Builder|Payroll wherePrevBalance($value)
 * @method static Builder|Payroll wherePreviousId($value)
 * @method static Builder|Payroll whereTill($value)
 * @method static Builder|Payroll whereTimeReportId($value)
 * @method static Builder|Payroll whereUpdatedAt($value)
 * @method static Builder|Payroll whereUserId($value)
 * @method static Builder|Payroll withTrashed()
 * @method static Builder|Payroll withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy([PayrollObserver::class])]
class Payroll extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'from' => 'date',
        'till' => 'date',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function timeReport(): BelongsTo
    {
        return $this->belongsTo(TimeReport::class);
    }

    public function previous(): BelongsTo
    {
        return $this->belongsTo(Payroll::class, 'previous_id');
    }

    public function next(): HasOne
    {
        return $this->hasOne(Payroll::class, 'previous_id');
    }
}
