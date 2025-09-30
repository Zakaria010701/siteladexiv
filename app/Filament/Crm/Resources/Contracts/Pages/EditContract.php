<?php

namespace App\Filament\Crm\Resources\Contracts\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Enums\Appointments\AppointmentStatus;
use App\Filament\Crm\Resources\Contracts\ContractResource;
use App\Models\Payment;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContract extends EditRecord
{

    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['credit_last_appointment'] = !is_null($this->getRecord()->creditedAppointment);
        $previous = $this->getRecord()->creditedAppointment ?? $this->getRecord()->customer->appointments()
            ->paid()
            ->status(AppointmentStatus::Done)
            ->where('start', '<', $data['date'])
            ->latest('start')
            ->first();

        if(!$data['credit_last_appointment'] && isset($previous)) {
            if($this->getRecord()->customer->contracts()->where('credited_appointment_id', $previous->id)->exists()) {
                $previous = null;
            }
        }
        $data['previous_id'] = $previous?->id;
        $data['treatments'] = $data['treatment_count'] - 1;
        $data['category_id'] = $this->getRecord()->services->first()?->category_id;

        $creditedPayments = $this->getRecord()->creditedPayments->pluck('id')->toArray();
        if(isset($previous)) {
            $data['credit_payments'] = $previous->payments()->doesntHave('customerCredit')
                ->get()
                ->map(fn (Payment $payment) => [
                    'id' => $payment->id,
                    'type' => $payment->type->value,
                    'amount' => $payment->amount,
                    'credit' => in_array($payment->id, $creditedPayments),
                ])
                ->toArray();
        }


        return $data;
    }
}
