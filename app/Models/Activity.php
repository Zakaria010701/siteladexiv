<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use App\Filament\Crm\Resources\Contracts\ContractResource;
use App\Filament\Crm\Resources\Customers\CustomerResource;
use App\Filament\Crm\Resources\Invoices\InvoiceResource;
use App\Filament\Crm\Resources\Vouchers\VoucherResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

/**
 * App\Models\Activity
 *
 * @property int $id
 * @property string|null $log_name
 * @property string $description
 * @property string|null $subject_type
 * @property string|null $event
 * @property int|null $customer_id
 * @property int|null $subject_id
 * @property string|null $causer_type
 * @property int|null $causer_id
 * @property Collection|null $properties
 * @property string|null $batch_uuid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|\Eloquent $causer
 * @property-read Customer|null $customer
 * @property-read Collection $changes
 * @property-read Model|\Eloquent $subject
 *
 * @method static Builder|Activity causedBy(Model $causer)
 * @method static Builder|Activity forBatch(string $batchUuid)
 * @method static Builder|Activity forEvent(string $event)
 * @method static Builder|Activity forSubject(Model $subject)
 * @method static Builder|Activity hasBatch()
 * @method static Builder|Activity inLog(...$logNames)
 * @method static Builder|Activity newModelQuery()
 * @method static Builder|Activity newQuery()
 * @method static Builder|Activity query()
 * @method static Builder|Activity whereBatchUuid($value)
 * @method static Builder|Activity whereCauserId($value)
 * @method static Builder|Activity whereCauserType($value)
 * @method static Builder|Activity whereCreatedAt($value)
 * @method static Builder|Activity whereCustomerId($value)
 * @method static Builder|Activity whereDescription($value)
 * @method static Builder|Activity whereEvent($value)
 * @method static Builder|Activity whereId($value)
 * @method static Builder|Activity whereLogName($value)
 * @method static Builder|Activity whereProperties($value)
 * @method static Builder|Activity whereSubjectId($value)
 * @method static Builder|Activity whereSubjectType($value)
 * @method static Builder|Activity whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Activity extends SpatieActivity
{
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getCustomerRoute(): ?string
    {
        if (! isset($this->customer)) {
            return null;
        }

        return CustomerResource::getUrl('edit', ['record' => $this->customer], panel: 'crm');
    }

    public function getSubjectRoute(): ?string
    {
        if($this->subject instanceof Appointment) {
            return AppointmentResource::getUrl('edit', ['record' => $this->subject], panel: 'crm');
        }

        if ($this->subject instanceof Customer) {
            return CustomerResource::getUrl('edit', ['record' => $this->subject], panel: 'crm');
        }

        if ($this->subject instanceof Invoice) {
            return InvoiceResource::getUrl('edit', ['record' => $this->subject], panel: 'crm');
        }

        if ($this->subject instanceof Contract) {
            return ContractResource::getUrl('edit', ['record' => $this->subject], panel: 'crm');
        }

        if ($this->subject instanceof Voucher) {
            return VoucherResource::getUrl('edit', ['record' => $this->subject], panel: 'crm');
        }

        return null;
    }

    public function getSubjectLabel()
    {
        if($this->subject instanceof Appointment) {
            return $this->subject->title;
        }

        if ($this->subject instanceof Customer) {
            return $this->subject->full_name;
        }

        if ($this->subject instanceof Invoice) {
            return $this->subject->invoice_number;
        }

        if ($this->subject instanceof Contract) {
            return $this->subject->title;
        }

        if ($this->subject instanceof Voucher) {
            return $this->subject->voucher_nr;
        }

        return null;
    }

    public function isDanger(): bool
    {
        if($this->subject instanceof Appointment) {
            if(!in_array($this->event, ['canceled', 'deleted', 'moved'])) {
                return false;
            }

            if($this->subject->start->diffInHours($this->created_at) > 24) {
                return false;
            }

            
        }

        return false;
    }

    public function getCauserRoute()
    {

    }

    public function getCauserLabel()
    {
        if($this->causer instanceof User) {
            return $this->causer->full_name;
        }

        return null;
    }
}
