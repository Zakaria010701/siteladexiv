<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $contract_id
 * @property int $service_id
 * @property string $price
 * @property string|null $meta
 * @property-read mixed $badge
 * @property-read Contract $contract
 * @property-read mixed $default_price
 * @property-read Service $service
 * @property-read Collection<int, ServiceCredit> $serviceCredits
 * @property-read int|null $service_credits_count
 * @property-read mixed $treatments_done
 * @property-read mixed $treatments_open
 *
 * @method static Builder|ContractService newModelQuery()
 * @method static Builder|ContractService newQuery()
 * @method static Builder|ContractService query()
 * @method static Builder|ContractService whereContractId($value)
 * @method static Builder|ContractService whereCreatedAt($value)
 * @method static Builder|ContractService whereId($value)
 * @method static Builder|ContractService whereMeta($value)
 * @method static Builder|ContractService wherePrice($value)
 * @method static Builder|ContractService whereServiceId($value)
 * @method static Builder|ContractService whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class ContractService extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['default_price', 'badge'];

    public function defaultPrice(): Attribute
    {
        return Attribute::make(fn () => $this->service->price);
    }

    public function badge(): Attribute
    {
        return Attribute::make(fn () => sprintf('%s (%s/%s)', $this->service->short_code, $this->treatments_open, $this->contract?->treatment_count));
    }

    public function treatmentsOpen(): Attribute
    {
        return Attribute::make(fn () => $this->getTreatmentsOpen());
    }

    public function getTreatmentsOpen(): int
    {
        return $this->serviceCredits->filter(fn (ServiceCredit $credit) => $credit->isUsed())->count();
    }

    public function treatmentsDone(): Attribute
    {
        return Attribute::make(fn () => $this->getTreatmentsDone());
    }

    public function getTreatmentsDone(): int
    {
        return $this->serviceCredits->filter(fn (ServiceCredit $credit) => $credit->isUsed())->count();
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function serviceCredits(): HasMany
    {
        return $this->hasMany(ServiceCredit::class);
    }
}
