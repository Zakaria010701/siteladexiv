<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $invoice_id
 * @property string|null $invoicable_type
 * @property int|null $invoicable_id
 * @property string $title
 * @property string|null $description
 * @property string $quantity
 * @property string|null $unit
 * @property string $unit_price
 * @property string $tax_percentage
 * @property string $tax
 * @property string $sub_total
 * @property string|null $meta
 * @property-read Model|\Eloquent|null $invoicable
 * @property-read Invoice $invoice
 *
 * @method static Builder<static>|InvoiceItem newModelQuery()
 * @method static Builder<static>|InvoiceItem newQuery()
 * @method static Builder<static>|InvoiceItem onlyTrashed()
 * @method static Builder<static>|InvoiceItem query()
 * @method static Builder<static>|InvoiceItem whereCreatedAt($value)
 * @method static Builder<static>|InvoiceItem whereDeletedAt($value)
 * @method static Builder<static>|InvoiceItem whereDescription($value)
 * @method static Builder<static>|InvoiceItem whereId($value)
 * @method static Builder<static>|InvoiceItem whereInvoicableId($value)
 * @method static Builder<static>|InvoiceItem whereInvoicableType($value)
 * @method static Builder<static>|InvoiceItem whereInvoiceId($value)
 * @method static Builder<static>|InvoiceItem whereMeta($value)
 * @method static Builder<static>|InvoiceItem whereQuantity($value)
 * @method static Builder<static>|InvoiceItem whereSubTotal($value)
 * @method static Builder<static>|InvoiceItem whereTax($value)
 * @method static Builder<static>|InvoiceItem whereTaxPercentage($value)
 * @method static Builder<static>|InvoiceItem whereTitle($value)
 * @method static Builder<static>|InvoiceItem whereUnit($value)
 * @method static Builder<static>|InvoiceItem whereUnitPrice($value)
 * @method static Builder<static>|InvoiceItem whereUpdatedAt($value)
 * @method static Builder<static>|InvoiceItem withTrashed()
 * @method static Builder<static>|InvoiceItem withoutTrashed()
 *
 * @mixin \Eloquent
 */
class InvoiceItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function invoicable(): MorphTo
    {
        return $this->morphTo('invoicable');
    }
}
