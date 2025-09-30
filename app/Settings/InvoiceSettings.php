<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class InvoiceSettings extends Settings
{
    public string $invoice_series;

    public string $proforma_series;

    public string $offer_series;

    public int $due_after_days;

    public ?array $default_header;

    public ?array $default_footer;

    public int $default_tax;

    public static function group(): string
    {
        return 'invoice';
    }
}
