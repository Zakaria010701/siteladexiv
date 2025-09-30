<?php

namespace App\Hooks\Appointments;

use App\Enums\Appointments\AppointmentModule;
use App\Enums\Invoices\InvoiceStatus;
use App\Enums\Invoices\InvoiceType;
use App\Enums\Transactions\PaymentType;
use App\Integration\GoogleCalendar\Event;
use App\Models\Appointment;
use App\Models\AppointmentItem;
use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServiceCredit;
use App\Support\Calculator;
use Illuminate\Support\Str;

class AfterUpdateAppointment
{
    public function __construct(private Appointment $appointment) {}

    public static function make(Appointment $appointment): self
    {
        return new self($appointment);
    }

    public function execute(): Appointment
    {

        $this->handleServiceCreditUpdate();
        $this->updatePayments();
        $this->createConsultationCredit();
        $this->updateGoogleEvent();
        $this->appointment->saveQuietly();

        return $this->appointment;
    }

    private function updatePayments(): void
    {
        if (! $this->appointment->status->isDone()) {
            return;
        }

        $this->appointment->payments->each(fn (Payment $payment) => $this->updatePayment($payment));
    }

     private function updateGoogleEvent(): void
    {
        if(!integration()->google_sync_calendar) {
            return;
        }

        if(is_null($this->appointment->google_event_id)) {
            return;
        }

        $event = Event::find($this->appointment->google_event_id);
        if(!isset($event)) {
            return;
        }

        $event->name = $this->appointment->title;
        $event->startDateTime = $this->appointment->start;
        $event->endDateTime = $this->appointment->end;
        $event->save();
    }

    private function createConsultationCredit(): void
    {
        // Only create Consultation Credits for Appointments of type Consultation
        if (! $this->appointment->type->isConsultation()) {
            return;
        }

        // Only create Consultation Credits for Appointments that have been paid
        if (! $this->appointment->appointmentOrder->status->isPaid()) {
            return;
        }

        if (! appointment()->consultation_fee_enabled || ! appointment()->consultation_fee_credits_enabled) {
            return;
        }

        $creditExists = $this->appointment->appointmentCredits()->where('amount', 30)->exists();
        if ($creditExists) {
            return;
        }

        $credit = $this->appointment->appointmentCredits()->create([
            'customer_id' => $this->appointment->customer_id,
            'amount' => 30,
            'description' => __('Consultation from :date', ['date' => formatDateTime($this->appointment->start)]),
        ]);
    }

    private function updatePayment(Payment $payment): void
    {
        $this->updateCreditPayment($payment);
        $this->handleInvoicePayment($payment);
    }

    private function updateCreditPayment(Payment $payment): void
    {
        if ($payment->type != PaymentType::Credit) {
            return;
        }

        $reference = $payment->reference;
        if (! $reference instanceof CustomerCredit) {
            return;
        }

        if (! is_null($reference->spent_at)) {
            return;
        }

        if ($reference->open_amount > 0) {
            return;
        }

        $reference->spent_at = now();
        $payment->reference->save();
    }

    private function handleInvoicePayment(Payment $payment)
    {
        if($payment->type != PaymentType::Invoice) {
            return;
        }

        if(isset($payment->reference) && $payment->reference instanceof Invoice) {
            return;
        }

        $type = $payment->pays_at_next_appointment ? InvoiceType::Proforma : InvoiceType::Invoice;
        $sequence = Invoice::where('series', $type->getSeries())->count() + 1;
        $date = $payment->pays_at_next_appointment
            ? ($this->appointment->next_appointment_date ?? $this->appointment->start)
            : $this->appointment->start;
        $tax = Calculator::getTaxAmmount($payment->amount, invoice()->default_tax, 2, false);
        $item = [
            'invoicable_type' => Appointment::class,
            'invoicable_id' => $this->appointment->id,
            'title' => sprintf("Services: %s", $this->appointment->getServices()->implode('short_code', ', ')),
            'quantity' => 1,
            'unit_price' => $payment->amount - $tax,
            'tax_percentage' => invoice()->default_tax,
            'tax' => $tax,
            'sub_total' => $payment->amount,
        ];
        /** @var Invoice */
        $invoice = $this->appointment->invoices()->create([
            'recipient_type' => Customer::class,
            'recipient_id' => $this->appointment->customer_id,
            'type' => $type,
            'status' => InvoiceStatus::Open,
            'series' => $type->getSeries(),
            'sequence' => $sequence,
            'invoice_number' => sprintf('%s-%s', $type->getSeries(), Str::of($sequence)->padLeft(5, 0)),
            'invoice_date' => $date->format('Y-m-d'),
            'due_date' => $date->copy()->addDays(invoice()->due_after_days)->format('Y-m-d'),
            'base_total' => $item['unit_price'],
            'discount_total' => 0,
            'net_total' => $item['unit_price'],
            'tax_total' => $item['tax'],
            'gross_total' => $item['sub_total'],
            'paid_total' => 0,
            'header' => invoice()->default_header,
            'footer' => invoice()->default_footer,
        ]);
        $invoice->items()->create($item);
        $payment->reference()->associate($invoice);
        $payment->save();
    }

    private function handleServiceCreditUpdate(): void
    {
        if (! $this->appointment->type->hasActiveModule(AppointmentModule::Order)) {
            return;
        }

        // The refresh is needed to get the current status of the order and current items
        $this->appointment->refresh();

        //$this->deleteOrphanedCredits();

        $this->appointment->appointmentItems
            ->where('purchasable_type', Service::class)
            ->whereNotNull('purchasable_id')
            ->each(function (AppointmentItem $item) {
                $item->adjustUsedServiceCredits();
            });
    }
}
