<?php

namespace App\Models;

use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use App\Filament\Crm\Resources\Contracts\ContractResource;
use App\Filament\Crm\Resources\Customers\CustomerResource;
use App\Filament\Crm\Resources\Invoices\InvoiceResource;
use App\Filament\Crm\Resources\Vouchers\VoucherResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TodoItem extends Model
{
    protected $guarded = ['id'];

    public $casts = [
        'completed_at' => 'datetime',
    ];

    public function completed(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => !is_null($attributes['completed_at'])
        );
    }

    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo('subject');
    }

    public function getSubjectUrl()
    {
        if ($this->subject == null) {
            return null;
        }

        if ($this->subject instanceof Appointment) {
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

    public function getSubjectTitle()
    {
        if ($this->subject == null) {
            return null;
        }

        if ($this->subject instanceof Appointment) {
            return $this->subject->title;
        }

        if ($this->subject instanceof Customer) {
            return $this->subject->full_name;
        }

        return null;
    }

    public function scopeCompleted(Builder $query)
    {
        $query->whereNotNull('completed_at');
    }
}
