<?php

namespace App\Filament\Crm\Resources\Appointments\Concerns;

use App\Actions\Appointments\ValidateServiceCredits;
use App\Enums\Appointments\AppointmentOrderStatus;
use App\Enums\Appointments\AppointmentStatus;
use App\Enums\TimeStep;
use App\Events\Appointments\AppointmentApprovedEvent;
use App\Events\Appointments\AppointmentCanceledEvent;
use App\Events\Appointments\AppointmentDoneEvent;
use App\Events\Appointments\AppointmentPendingEvent;
use App\Hooks\Appointments\AfterUpdateAppointment;
use App\Models\Appointment;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Throwable;

use function Filament\Support\is_app_url;

trait HandlesAppointmentUpdate
{
    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true, array $options = []): void
    {
        $this->authorizeAccess();

        try {
            $this->beginDatabaseTransaction();

            $this->callHook('beforeValidate');

            $data = $this->form->getState(afterValidate: function () {
                $this->callHook('afterValidate');
            });

            $data = $this->mutateFormDataBeforeSave($data);

            $this->beforeSave($data, $options);

            $this->handleRecordUpdate($this->getRecord(), $data);

            $this->callHook('afterSave');

            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $this->rollBackDatabaseTransaction() :
                $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        $this->rememberData();

        if ($shouldSendSavedNotification) {
            $this->getSavedNotification()?->send();
        }

        if ($shouldRedirect && ($redirectUrl = $this->getRedirectUrl())) {
            $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode() && is_app_url($redirectUrl));
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['status'] = $this->getAppointmentStatus($data);

        if (empty($data['next_appointment_step'])) {
            return $data;
        }
        $step = TimeStep::from($data['next_appointment_step']);
        if ($step == TimeStep::None) {
            return $data;
        }
        $data['next_appointment_date'] = $step->add(Carbon::parse($data['start']), $data['next_appointment_in']);

        return $data;
    }

    private function getAppointmentStatus(array $data): AppointmentStatus
    {
        if (! empty($data['done_at'])) {
            return AppointmentStatus::Done;
        }

        if (! empty($data['canceled_at'])) {
            return AppointmentStatus::Canceled;
        }

        if (! empty($this->getRecord()->approved_at)) {
            return AppointmentStatus::Approved;
        }

        return AppointmentStatus::Pending;
    }

    protected function dispatchStatusChangeEvent(AppointmentStatus $old, AppointmentStatus $new, Appointment $record): void
    {
        if ($old == $new) {
            return;
        }

        $properties = [
            'old' => ['status' => $old->value],
            'attributes' => ['status' => $new],
        ];

        switch ($new) {
            case AppointmentStatus::Done:
                $properties['attributes']['done_at'] = $record->done_at;
                AppointmentDoneEvent::dispatch($record, auth()->user(), true);
                break;
            case AppointmentStatus::Canceled:
                $properties['attributes']['cancel_reason'] = $record->cancel_reason?->getLabel();
                $properties['attributes']['canceled_at'] = $record->canceled_at;
                AppointmentCanceledEvent::dispatch($record, auth()->user(), $record->cancel_reason, true);
                break;
            case AppointmentStatus::Approved:
                $properties['attributes']['approved_at'] = $record->approved_at;
                AppointmentApprovedEvent::dispatch($record, auth()->user(), false);
                break;
            case AppointmentStatus::Pending:
                AppointmentPendingEvent::dispatch($record, auth()->user(), false);
        }

        activity()
            ->event($new->value)
            ->on($record)
            ->withProperties($properties)
            ->causedBy(auth()->user())
            ->log($new->value);
    }

    /**
     * @throws Halt
     */
    protected function beforeSave(array $data, array $options = []): void
    {
        if(!($options['saveWithoutCreditsValidation'] ?? false)) {
            ValidateServiceCredits::make($this->getRecord());
        }

        if(!($options['saveWithoutPaymentValidation'] ?? false)) {
            $this->validateAppointmentPaid($data);
        }
    }
    /**
     * @throws Halt
     */
    private function validateAppointmentPaid(array $data): void
    {
        if($data['status'] !== AppointmentStatus::Done->value && $data['status'] !== AppointmentStatus::Done) {
            return;
        }

        if(in_array($this->getRecord()->appointmentOrder->status, [AppointmentOrderStatus::Paid, AppointmentOrderStatus::Canceled])) {
            return;
        }

        Notification::make()
            ->danger()
            ->duration('persistent')
            ->color('danger')
            ->title(__("Appointment order is not paid"))
            ->send();

        throw (new Halt())->rollBackDatabaseTransaction();
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var AppointmentStatus */
        $oldStatus = $record->status;
        $record->update($data);

        $this->dispatchStatusChangeEvent($oldStatus, $record->status, $record);

        return $record;
    }

    protected function afterSave(): void
    {
        AfterUpdateAppointment::make($this->getRecord())->execute();
    }
}
