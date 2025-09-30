<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Enums\Appointments\AppointmentItemType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\AppointmentItem
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $appointment_id
 * @property string|null $purchasable_type
 * @property int|null $purchasable_id
 * @property string $description
 * @property string|null $note
 * @property float $unit_price
 * @property float $quantity
 * @property float $used
 * @property float $discount_total
 * @property float $sub_total
 * @property array|null $meta
 * @property-read Appointment|null $appointment
 * @property-read Model|\Eloquent $purchasable
 *
 * @method static Builder|AppointmentItem newModelQuery()
 * @method static Builder|AppointmentItem newQuery()
 * @method static Builder|AppointmentItem query()
 * @method static Builder|AppointmentItem whereAppointmentId($value)
 * @method static Builder|AppointmentItem whereCreatedAt($value)
 * @method static Builder|AppointmentItem whereDescription($value)
 * @method static Builder|AppointmentItem whereDiscountTotal($value)
 * @method static Builder|AppointmentItem whereId($value)
 * @method static Builder|AppointmentItem whereMeta($value)
 * @method static Builder|AppointmentItem whereNote($value)
 * @method static Builder|AppointmentItem wherePurchasableId($value)
 * @method static Builder|AppointmentItem wherePurchasableType($value)
 * @method static Builder|AppointmentItem whereQuantity($value)
 * @method static Builder|AppointmentItem whereSubTotal($value)
 * @method static Builder|AppointmentItem whereUnitPrice($value)
 * @method static Builder|AppointmentItem whereUpdatedAt($value)
 * @method static Builder|AppointmentItem whereUsed($value)
 *
 * @property-read Collection<int, ServiceCredit> $orderedServiceCredits
 * @property-read int|null $ordered_service_credits_count
 * @property-read Collection<int, ServiceCredit> $usedServiceCredits
 * @property-read int|null $used_service_credits_count
 * @property AppointmentItemType|null $type
 * @property-read mixed $badge
 *
 * @method static Builder|AppointmentItem whereType($value)
 *
 * @mixin \Eloquent
 */
class AppointmentItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => AppointmentItemType::class,
        'meta' => 'array',
        'unit_price' => 'float',
        'quantity' => 'float',
        'used' => 'float',
        'discount_total' => 'float',
        'sub_total' => 'float',
    ];

    public function badge(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (isset($this->purchasable?->short_code)) {
                    return sprintf('%s %s/%s', $this->purchasable->short_code, $this->used, $this->quantity);
                }

                return sprintf('%s %s/%s', $this->description, $this->used, $this->quantity);
            }
        );
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function purchasable(): MorphTo
    {
        return $this->morphTo('purchasable');
    }

    public function orderedServiceCredits(): MorphMany
    {
        return $this->morphMany(ServiceCredit::class, 'source');
    }

    public function usedServiceCredits(): MorphMany
    {
        return $this->morphMany(ServiceCredit::class, 'usage');
    }

    public function isService(): bool
    {
        return $this->purchasable_type == Service::class && ! is_null($this->purchasable_id);
    }

    public function adjustUsedServiceCredits(): void
    {
        // Get the current counts from the database.
        $used = $this->usedServiceCredits()->count();
        // Associate existing Service Credits with the item.
        $this->addMissingUsedServiceCredits($used);
        // Dissasociate Service Credits from the item.
        $this->removeSurplusUsedServiceCredits($used);
        $this->adjustUsedServiceCredit();
    }

    private function addMissingUsedServiceCredits(int $current): void
    {
        if(is_null($this->appointment)) {
            return;
        }

        if (! $this->appointment->status->isDone()) {
            return;
        }

        if ($current < intval($this->used)) {
            $associate = $this->used - $current;
            $credits = $this->appointment->customer->serviceCredits()
                ->where('service_id', $this->purchasable_id)
                ->unused()
                ->oldest()
                ->limit(intval($associate))
                ->get()
                ->map(function (ServiceCredit $credit) {
                    $credit->used_at = now();

                    return $credit;
                });
            $this->usedServiceCredits()->saveMany($credits);
        }
    }

    private function removeSurplusUsedServiceCredits(int $current): void
    {
        if ($current > $this->used) {
            $dissociate = $current - $this->used;
            $this->usedServiceCredits()
                ->orderBy('used_at')
                ->limit(intval($dissociate))
                ->update([
                    'used_at' => null,
                    'usage_type' => null,
                    'usage_id' => null,
                ]);
        }
    }

    private function adjustUsedServiceCredit(): void
    {
        $current = $this->usedServiceCredits()->count();
        if ($current != $this->used) {
            $this->used = $current;
            $this->save();
        }
    }
}
