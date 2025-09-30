<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CompanySettings extends Settings
{
    public string $name;

    public string $phone;

    public string $email;

    public string $location;

    public string $postcode;

    public string $address;

    public ?string $website;

    public ?string $vat_id;

    public ?string $tax_id;

    public ?string $bank_name;

    public ?string $bank_iban;

    public ?string $bank_bic;

    public ?string $logo_path;

    public static function group(): string
    {
        return 'company';
    }
}
