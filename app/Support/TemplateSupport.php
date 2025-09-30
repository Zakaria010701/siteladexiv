<?php

namespace App\Support;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Invoice;
use App\Settings\GeneralSettings;
use Filament\Forms\Components\RichEditor\RichContentRenderer;

class TemplateSupport
{
    private readonly array $placeholders;

    public function __construct(
        protected ?Appointment $appointment = null,
        protected ?Customer $customer = null,
        protected ?Invoice $invoice = null
    ) {
        if (is_null($this->customer)) {
            $this->customer = $this->getCustomer();
        }

        $this->placeholders = $this->getPlaceholderValues();
    }

    public static function make(
        ?Appointment $appointment = null,
        ?Customer $customer = null,
        ?Invoice $invoice = null
    ): self {
        return new self($appointment, $customer, $invoice);
    }

    public function formatTemplate(?array $template): string
    {

        if (empty($template)) {
            return '';
        }
        return RichContentRenderer::make($template)
            ->mergeTags($this->placeholders)
            ->toHtml();
    }

    public function formatTemplateText(?array $template): string
    {
        if (empty($template)) {
            return '';
        }
        $template = $this->parsePlaceholders($template);

        return '';
    }

    protected function getCustomer(): ?Customer
    {
        if (isset($this->customer)) {
            return $this->customer;
        }

        if (isset($this->appointment->customer)) {
            return $this->appointment->customer;
        }

        if (isset($this->invoice) && $this->invoice->recipient instanceof Customer) {
            return $this->invoice->recipient;
        }

        return null;
    }

    protected function parsePlaceholders(array $content): array
    {
        if ($content['type'] == 'mergeTag') {
            $content['type'] = 'text';
            $content['text'] = $this->placeholders[$content['attrs']['id']] ?? '';

            return $content;
        }
        if (empty($content['content'])) {
            return $content;
        }

        $content['content'] = collect($content['content'])
            ->map(fn (array $item): array => self::parsePlaceholders($item))
            ->toArray();

        return $content;
    }

    public function getPlaceholderValues(): array
    {
        return array_merge(
            $this->getAppointmentPlaceholderValues(),
            $this->getCustomerPlaceholderValues(),
            $this->getInvoicePlaceholderValues(),
            $this->getCompanyPlaceholderValues(),
        );
    }

    public function getPlaceholderNames(): array
    {
        return array_keys($this->getPlaceholderValues());
    }

    protected function getAppointmentPlaceholderValues(): array
    {
        $general = app(GeneralSettings::class);

        return [
            'appointment_date' => $this->appointment?->start?->format($general->date_format),
            'appointment_time' => $this->appointment?->arrivalTime?->format($general->time_format),
        ];
    }

    protected function getCustomerPlaceholderValues(): array
    {
        return [
            'customer_first_name' => $this->customer?->firstname,
            'customer_last_name' => $this->customer?->lastname,
            'customer_full_name' => $this->customer?->full_name,
            'customer_email' => $this->customer?->email,
            'customer_phone' => $this->customer?->phone_number,
        ];
    }

    protected function getInvoicePlaceholderValues(): array
    {
        return [
            'invoice_date' => $this->invoice?->invoice_date?->format(general()->date_format),
            'invoice_number' => $this->invoice?->invoice_number,
            'invoice_due_date' => $this->invoice?->due_date?->format(general()->date_format),
            'invoice_status' => $this->invoice?->status?->getLabel(),
            'invoice_gross' => formatMoney($this->invoice?->gross_total),
        ];
    }

    protected function getCompanyPlaceholderValues(): array
    {
        return [
            'company_email' => company()->email,
            'company_address' => sprintf('%s, %s %s', company()->address, company()->postcode, company()->location),
            'company_street' => company()->address,
            'company_postcode' => company()->postcode,
            'company_location' => company()->location,
            'company_bank_bic' => company()->bank_bic,
            'company_bank_iban' => company()->bank_iban,
            'company_bank_name' => company()->bank_name,
            'company_phone' => company()->phone,
            'company_website' => company()->website,
        ];
    }
}
