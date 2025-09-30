<?php

namespace App\Filament\Crm\Resources\Appointments\Pages;

use App\Filament\Crm\Resources\Appointments\Concerns\HasApproveAppointmentAction;
use App\Filament\Crm\Resources\Appointments\Concerns\HasCancelAppointmentAction;
use App\Filament\Crm\Resources\Appointments\Concerns\HasSaveWithoutPaymentAction;
use App\Filament\Crm\Resources\Appointments\Concerns\HandlesAppointmentUpdate;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Actions\Appointments\ValidateServiceCredits;
use App\Enums\Appointments\AppointmentDeleteReason;
use App\Enums\Appointments\AppointmentOrderStatus;
use App\Enums\Appointments\AppointmentStatus;
use App\Enums\Appointments\CancelReason;
use App\Enums\TimeStep;
use App\Events\Appointments\AppointmentApprovedEvent;
use App\Events\Appointments\AppointmentCanceledEvent;
use App\Events\Appointments\AppointmentDoneEvent;
use App\Events\Appointments\AppointmentPendingEvent;
use App\Filament\Actions\Customer\MergeAction;
use App\Filament\Actions\ReportBugAction;
use App\Filament\Concerns\HasSaveAndCloseAction;
use App\Filament\Crm\Concerns\CheckCustomerValid;
use App\Filament\Crm\Concerns\HandlesServiceCreditUpdate;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use App\Filament\Crm\Resources\AppointmentResource\Concerns;
use App\Hooks\Appointments\AfterUpdateAppointment;
use App\Models\Appointment;
use App\Support\Appointment\AppointmentCalculator;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use Illuminate\Http\Request;
use Throwable;

use function Filament\Support\is_app_url;

/**
 * @property \Filament\Schemas\Schema $form
 */
class EditAppointment extends EditRecord
{
    use CheckCustomerValid;
    use HandlesServiceCreditUpdate;
    use HasSaveAndCloseAction;
    use HasApproveAppointmentAction;
    use HasCancelAppointmentAction;
    use HasSaveWithoutPaymentAction;
    use HandlesAppointmentUpdate;

    protected static string $resource = AppointmentResource::class;

    #[On('merge-customer')]
    public function mergeCustomer()
    {
        $this->mountAction('merge');
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecordTitle();
    }

    public function getRecord(): Appointment
    {
        /** @var Appointment $data */
        $data = $this->record;

        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getSaveAndCloseFormAction(),
            $this->getSaveWithoutPaymentAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getApproveAppointmentAction(),
            $this->getCancelAppointmentAction(),
            DeleteAction::make()
                ->before(fn () => null)
                ->schema([
                    Select::make('delete_reason')
                        ->live()
                        ->required()
                        ->options(AppointmentDeleteReason::class),
                    Textarea::make('delete_note')
                        ->required(),
                ])
                ->using(function (array $data, Appointment $record) {
                    $reason = AppointmentDeleteReason::from($data['delete_reason']);
                    $record->delete();
                }),
            ForceDeleteAction::make(),
            RestoreAction::make(),
            ReportBugAction::make()
                ->reportUrl($this->getUrl(['record' => $this->getRecord()])),
        ];
    }

    protected function mergeAction(): Action
    {
        return MergeAction::make('merge');
    }

    protected function afterFill(): void
    {
        if (! $this->record instanceof Appointment) {
            return;
        }

        $this->data['services'] = $this->record->getServices()->pluck('id')->toArray();

        if($this->record->status->isDone()) {
            $this->data = AppointmentCalculator::make($this->record, $this->data)->updatedCustomer()->calculate()->saveData();
        } else {
            $this->data = AppointmentCalculator::make($this->record, $this->data)->updatedCustomer()->updatedPrices()->saveData();
        }

        $this->checkCustomer($this->getRecord());
    }

    protected function checkCustomer(Appointment $appointment): void
    {
        if ($appointment->isPending()) {
            return;
        }

        $customer = $appointment->customer;

        if (is_null($customer)) {
            return;
        }

        $this->checkCustomerValid($this->getRecord()->customer);
    }
}
