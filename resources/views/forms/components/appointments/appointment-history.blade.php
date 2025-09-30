@php
    $appointments = $getRecord()->appointments()->orderByDesc('start')->limit(5)->get();
@endphp
<div {{ $attributes }}>
    <table class="text-sm w-full">
        @foreach($appointments as $appointment)
            <tr>
                <td class="flex flex-row justify-between">
                    <div class="grow">
                        <x-filament::link href="{{\App\Filament\Crm\Resources\Appointments\AppointmentResource::getUrl('edit', ['record' => $appointment])}}">
                            {{formatDate($appointment->start)}} {{$appointment->category?->short_code}}
                        </x-filament::link>
                        <br><span class="text-xs text-gray-500">{{ $appointment->getServices()->implode('short_code', ', ') }}</span>
                    </div>
                    <div class="text-right">
                        {{ formatMoney($appointment->appointmentOrder?->gross_total) }}
                        @foreach($appointment->payments as $payment)
                            <br><span class="text-xs text-gray-500">
                                {{ formatMoney($payment->amount) }} {{$payment->type->getLabel()}}
                            </span>
                        @endforeach
                    </div>
                </td>
            </tr>
        @endforeach
    </table>
</div>
