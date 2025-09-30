<div>
    {!! \App\Support\TemplateSupport::make(
        appointment: App\Models\Appointment::first(),
        customer: App\Models\Customer::first(),
        invoice: App\Models\Invoice::first(),
    )->formatTemplate($getState()) !!}
</div>
