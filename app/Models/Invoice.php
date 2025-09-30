<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Enums\Invoices\InvoiceStatus;
use App\Enums\Invoices\InvoiceType;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use App\Filament\Crm\Resources\Customers\CustomerResource;
use App\Observers\InvoiceObserver;
use App\Support\TemplateSupport;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string $recipient_type
 * @property int $recipient_id
 * @property string|null $source_type
 * @property int|null $source_id
 * @property InvoiceType $type
 * @property InvoiceStatus $status
 * @property string $series
 * @property int $sequence
 * @property string $invoice_number
 * @property string|Carbon $invoice_date
 * @property string|Carbon|null $due_date
 * @property string $base_total
 * @property string $discount_total
 * @property string $net_total
 * @property string $tax_total
 * @property string $gross_total
 * @property string $paid_total
 * @property array|null $header
 * @property array|null $footer
 * @property array|null $meta
 * @property-read Collection<int, InvoiceItem> $items
 * @property-read int|null $items_count
 * @property-read Model|\Eloquent $recipient
 * @property-read Model|\Eloquent $source
 *
 * @method static Builder|Invoice newModelQuery()
 * @method static Builder|Invoice newQuery()
 * @method static Builder|Invoice onlyTrashed()
 * @method static Builder|Invoice query()
 * @method static Builder|Invoice whereBaseTotal($value)
 * @method static Builder|Invoice whereCreatedAt($value)
 * @method static Builder|Invoice whereDeletedAt($value)
 * @method static Builder|Invoice whereDiscountTotal($value)
 * @method static Builder|Invoice whereDueDate($value)
 * @method static Builder|Invoice whereFooter($value)
 * @method static Builder|Invoice whereGrossTotal($value)
 * @method static Builder|Invoice whereHeader($value)
 * @method static Builder|Invoice whereId($value)
 * @method static Builder|Invoice whereInvoiceDate($value)
 * @method static Builder|Invoice whereInvoiceNumber($value)
 * @method static Builder|Invoice whereMeta($value)
 * @method static Builder|Invoice whereNetTotal($value)
 * @method static Builder|Invoice wherePaidTotal($value)
 * @method static Builder|Invoice whereRecipientId($value)
 * @method static Builder|Invoice whereRecipientType($value)
 * @method static Builder|Invoice whereSequence($value)
 * @method static Builder|Invoice whereSeries($value)
 * @method static Builder|Invoice whereSourceId($value)
 * @method static Builder|Invoice whereSourceType($value)
 * @method static Builder|Invoice whereStatus($value)
 * @method static Builder|Invoice whereTaxTotal($value)
 * @method static Builder|Invoice whereType($value)
 * @method static Builder|Invoice whereUpdatedAt($value)
 * @method static Builder|Invoice withTrashed()
 * @method static Builder|Invoice withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy(InvoiceObserver::class)]
class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => InvoiceStatus::class,
        'type' => InvoiceType::class,
        'invoice_date' => 'date',
        'due_date' => 'date',
        'header' => 'array',
        'footer' => 'array',
        'meta' => 'array',
    ];

    public function recipient(): MorphTo
    {
        return $this->morphTo('recipient');
    }

    public function source(): MorphTo
    {
        return $this->morphTo('source');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function getRecipientUrl(): ?string
    {
        if ($this->recipient == null) {
            return null;
        }

        if ($this->recipient instanceof Customer) {
            return CustomerResource::getUrl('edit', ['record' => $this->recipient]);
        }

        return null;
    }

    public function getRecipientTitle(): ?string
    {
        if ($this->recipient == null) {
            return null;
        }

        if ($this->recipient instanceof Customer) {
            return $this->recipient->full_name;
        }

        return null;
    }

    public function getSourceUrl(): ?string
    {
        if ($this->source == null) {
            return null;
        }

        if ($this->source instanceof Appointment) {
            return AppointmentResource::getUrl('edit', ['record' => $this->source]);
        }

        return null;
    }

    public function getSourceTitle(): ?string
    {
        if ($this->source == null) {
            return null;
        }

        if ($this->source instanceof Appointment) {
            return $this->source->title;
        }

        return null;
    }

    public function getHeader(): string
    {
        if ($this->recipient instanceof Customer) {
            return TemplateSupport::make(customer: $this->recipient, invoice: $this)->formatTemplate($this->header);
        }

        return TemplateSupport::make(invoice: $this)->formatTemplate($this->header);
    }

    public function getFooter(): string
    {
        if ($this->recipient instanceof Customer) {
            return TemplateSupport::make(customer: $this->recipient, invoice: $this)->formatTemplate($this->footer);
        }

        return TemplateSupport::make(invoice: $this)->formatTemplate($this->footer);
    }

    public function scopeDue(Builder $query): void
    {
        $query->where('due_date', '<=', today())->whereIn('status', [InvoiceStatus::Open, InvoiceStatus::Reminder]);
    }

    public function scopeNotDue(Builder $query): void
    {
        $query->where('due_date', '>', today())->whereNotIn('status', [InvoiceStatus::Open, InvoiceStatus::Reminder]);
    }

    public function scopeStatus(Builder $query, InvoiceStatus $status): void
    {
        $query->where('status', $status);
    }

    public function scopeOpen(Builder $query): void
    {
        $query->where('status', InvoiceStatus::Open);
    }

    public function scopeType(Builder $query, InvoiceType $type): void
    {
        $query->where('type', $type);
    }
}
