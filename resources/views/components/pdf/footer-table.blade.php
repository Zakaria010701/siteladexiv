<div style="position: absolute; bottom: 0; line-height: 1.0; font-weight: 400; width: 100%;">
    <table style="border-collapse: collapse; width: 100%; margin-bottom: 0.3rem; color: #212529; font-size: 9px">
        <tr>
            <td style="padding: 0.2rem; vertical-align: top;">
                <p style="margin-bottom: 0.3rem;">{{ company()->name }}</p>
                <p style="margin-bottom: 0.3rem;">{{ company()->address }}</p>
                <p style="margin-bottom: 0.3rem;">{{ company()->postcode }} {{ company()->address }}</p>
                <p style="margin-bottom: 0.3rem;">{{ __('Web') }}: {{ company()->website }}</p>
            </td>
            <td style="padding: 0.2rem; vertical-align: top; line-height: 1.0;">
                <p style="margin-bottom: 0.3rem;">{{ __('Tel.') }}: {{ company()->phone }}</p>
                <p style="margin-bottom: 0.3rem;">{{ company()->email }}</p>
                <p style="margin-bottom: 0.3rem;">{{ __('Tax Id') }}: {{ company()->tax_id }}</p>
                <p style="margin-bottom: 0.3rem;">{{ __('Vat Id') }}: {{ company()->vat_id }}</p>
            </td>
            <td style="padding: 0.2rem; vertical-align: top; line-height: 1.0;">
                <p style="margin-bottom: 0.3rem;">{{ __('Bank') }}: {{ company()->bank_name }}</p>
                <p style="margin-bottom: 0.3rem;">{{ __('Iban') }}: {{ company()->bank_iban }}</p>
                <p style="margin-bottom: 0.3rem;">{{ __('Bic') }}: {{ company()->bank_bic }}</p>
            </td>
        </tr>
    </table>
</div>