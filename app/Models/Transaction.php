<?php

namespace App\Models;

use App\Enums\Transactions\TransactionStatus;
use App\Enums\Transactions\TransactionType;
use App\Filament\Crm\Resources\Invoices\InvoiceResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'type' => TransactionType::class,
        'status' => TransactionStatus::class,
        'date' => 'datetime',
        'meta' => 'array',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function bookable(): MorphTo
    {
        return $this->morphTo('bookable');
    }

    public function customer(): HasOneThrough
    {
        return $this->hasOneThrough(Customer::class, Account::class, 'id', 'id', 'account_id', 'customer_id');
    }

    public function fintsImport(): BelongsTo
    {
        return $this->belongsTo(FintsImport::class);
    }

    public function getBookableUrl(): ?string
    {
        if ($this->bookable == null) {
            return null;
        }

        if ($this->bookable instanceof Invoice) {
            return InvoiceResource::getUrl('edit', ['record' => $this->bookable]);
        }

        return null;
    }

    public function getBookableTitle(): ?string
    {
        if ($this->bookable == null) {
            return null;
        }

        if ($this->bookable instanceof Invoice) {
            return $this->bookable->invoice_number;
        }

        return null;
    }
}
