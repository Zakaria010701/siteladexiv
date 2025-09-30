<?php

namespace App\Filament\Actions\Appointments;

use Filament\Support\Enums\Width;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Section;
use App\Actions\Appointments\BookAppointment;
use App\Actions\Appointments\CalculateDuration;
use App\Enums\Appointments\AppointmentDeleteReason;
use App\Enums\Appointments\AppointmentStatus;
use App\Enums\Appointments\AppointmentType;
use App\Enums\Gender;
use App\Enums\Notifications\NotificationType;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use App\Filament\Crm\Resources\Customers\Forms\CustomerForm;
use App\Forms\Components\ItemActions;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\NotificationTemplate;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\WorkTime;
use App\Support\Appointment\AppointmentCalculator;
use App\Support\Appointment\BookingCalculator;
use App\Support\AvailabilitySupport;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class ViewAppointmentAction extends CreateAction
{
    public static function getDefaultName(): ?string
    {
        return 'view-appointment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorize(AppointmentResource::canCreate());

        $this->model(Appointment::class);

        $this->modalHeading(fn (Appointment $record) => sprintf('%s (%s) %s', $record->start->format('H:i'), $record->type->getLabel(), $record->category->name ?? ''));

        $this->registerModalActions([
            $this->checkInAction(),
        ]);

        $this->schema([
            Grid::make(3)
                ->schema([
                    TextEntry::make('start')
                        ->time(general()->time_format),
                    TextEntry::make('duration'),
                    TextEntry::make('status')
                        ->badge(),
                    TextEntry::make('appointmentItems.description')
                        ->label(__('Items')),
                    TextEntry::make('user.name'),
                    Fieldset::make(__('Customer'))
                        ->relationship('customer')
                        ->columns(3)
                        ->schema([
                            TextEntry::make('full_name'),
                            TextEntry::make('email'),
                            TextEntry::make('phone_number'),
                        ]),
                    TextEntry::make('description')
                        ->columnSpanFull()
                        ->placeholder(__('No Description')),
                ]),
            Toggle::make('check_in_at')
                ->live()
                ->label(fn (Appointment $record) => $record->checked_in ? __('Checked in at :time', [
                    'time' => formatDateTime($record->check_in_at),
                ]) : __('Check in'))
                ->formatStateUsing(fn (Appointment $record) => $record->checked_in)
                ->afterStateUpdated(fn (Component $livewire) => $livewire->mountAction('checkIn')),
            Toggle::make('check_out_at')
                ->live()
                ->label(fn (Appointment $record) => $record->checked_out ? __('Checked out at :time', [
                    'time' => formatDateTime($record->check_out_at),
                ]) : __('Check out'))
                ->formatStateUsing(fn (Appointment $record) => $record->checked_out)
                ->afterStateUpdated(fn (Component $livewire) => $livewire->mountAction('checkOut')),
            Toggle::make('controlled_at')
                ->live()
                ->label(fn (Appointment $record) => $record->controlled ? __('Controlled at :time', [
                    'time' => formatDateTime($record->controlled_at),
                ]) : __('Verification'))
                ->formatStateUsing(fn (Appointment $record) => $record->controlled)
                ->afterStateUpdated(fn (Component $livewire) => $livewire->mountAction('control')),
            Toggle::make('confirmed_at')
                ->live()
                ->label(fn (Appointment $record) => $record->confirmed ? __('Confirmed at :time', [
                    'time' => formatDateTime($record->confirmed_at),
                ]) : __('Confirm'))
                ->formatStateUsing(fn (Appointment $record) => $record->confirmed)
                ->afterStateUpdated(fn (Component $livewire) => $livewire->mountAction('confirm')),
        ]);

        $this->modalSubmitAction(false);

        $this->extraModalFooterActions([
            Action::make('edit')
                ->label(__('Edit'))
                ->icon('heroicon-s-pencil')
                ->url(fn (Appointment $record) => AppointmentResource::getUrl('edit', ['record' => $record])),
            Action::make('move')
                ->label(__('Move'))
                ->icon('heroicon-s-arrows-pointing-out')
                ->action(function () {
                    Notification::make()
                        ->title(__('Startet move'))
                        ->color('success')
                        ->send();
                    $this->dispatch('filament-fullcalendar--enable-move');
                })
                ->cancelParentActions(),
            //$this->getApproveAppointmentAction(),
            /*$this->getCancelAppointmentAction()
                ->cancelParentActions()
                ->after(fn () => $this->dispatch('filament-fullcalendar--reload')),*/
            DeleteAction::make()
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
                })
                ->icon('heroicon-s-trash'),
            ]);
    }

    public function checkInAction(): Action
    {
        return Action::make('checkIn')
            ->label(fn (Appointment $record) => $record->checked_in ? __('Checked in at :time', [
                'time' => formatDateTime($record->check_in_at),
            ]) : __('Check in'))
            ->modalDescription(__('filament-actions::modal.confirmation'))
            ->extraModalFooterActions(fn (Appointment $record, Action $action) => [
                $action->makeModalSubmitAction('checkInAndSend', arguments: ['send_notification' => true])
                    ->visible(fn () => NotificationTemplate::query()
                        ->where('type', NotificationType::AppointmentCheckIn)
                        ->whereHas('branches', fn (Builder $query) => $query->where('branches.id', $record->branch_id))
                        ->first()?->enable_mail ?? false)
                    ->label(__('Check in with Notification'))
                    ->icon('heroicon-o-envelope')
                    ->color('primary'),
            ])
            ->action(function (Appointment $record, array $arguments) {
                if($record->checked_in) {
                    $record->check_in_at = null;
                    $record->save();
                } else {
                    $record->markCheckedIn($arguments['send_notification'] ?? false);
                    Notification::make()
                        ->title(__('Checked in at :time', [
                            'time' => formatDateTime($record->check_in_at),
                        ]))
                        ->success()
                        ->send();
                }
            });
    }

    public function checkOutAction(): Action
    {
        return Action::make('checkOut')
            ->label(fn (Appointment $record) => $record->checked_out ? __('Checked out at :time', [
                'time' => formatDateTime($record->check_out_at),
            ]) : __('Check out'))
            ->modalDescription(__('filament-actions::modal.confirmation'))
            ->extraModalFooterActions(fn (Appointment $record, Action $action) => [
                $action->makeModalSubmitAction('checkOutAndSend', arguments: ['send_notification' => true])
                    ->visible(fn () => NotificationTemplate::query()
                        ->where('type', NotificationType::AppointmentCheckOut)
                        ->whereHas('branches', fn (Builder $query) => $query->where('branches.id', $record->branch_id))
                        ->first()?->enable_mail ?? false)
                    ->label(__('Check out with Notification'))
                    ->icon('heroicon-o-envelope')
                    ->color('primary'),
            ])
            ->action(function (Appointment $record, array $arguments) {
                if($record->checked_out) {
                    $record->check_out_at = null;
                    $record->save();
                } else {
                    $record->markCheckedOut($arguments['send_notification'] ?? false);
                    Notification::make()
                        ->title(__('Check out at :time', [
                            'time' => formatDateTime($record->check_out_at),
                        ]))
                        ->success()
                        ->send();
                }
            });
    }

    public function controlAction(): Action
    {
        return Action::make('control')
            ->label(fn (Appointment $record) => $record->controlled ? __('Controlled at :time', [
                'time' => formatDateTime($record->controlled_at),
            ]) : __('Verification'))
            ->modalDescription(__('filament-actions::modal.confirmation'))
            ->extraModalFooterActions(fn (Appointment $record, Action $action) => [
                $action->makeModalSubmitAction('controllAndSend', arguments: ['send_notification' => true])
                    ->visible(fn () => NotificationTemplate::query()
                        ->where('type', NotificationType::AppointmentControlled)
                        ->whereHas('branches', fn (Builder $query) => $query->where('branches.id', $record->branch_id))
                        ->first()?->enable_mail ?? false)
                    ->label(__('Controll with Notification'))
                    ->icon('heroicon-o-envelope')
                    ->color('primary'),
            ])
            ->action(function (Appointment $record, array $arguments) {
                if($record->controlled) {
                    $record->controlled_at = null;
                    $record->save();
                } else {
                    $record->markControlled($arguments['send_notification'] ?? false);
                    Notification::make()
                        ->title(__('Controlled at :time', [
                            'time' => formatDateTime($record->controlled_at),
                        ]))
                        ->success()
                        ->send();
                }
            });
    }

    public function confirmAction(): Action
    {
        return Action::make('confirm')
            ->label(fn (Appointment $record) => $record->confirmed ? __('Confirmed at :time', [
                'time' => formatDateTime($record->confirmed_at),
            ]) : __('Confirm'))
            ->modalDescription(__('filament-actions::modal.confirmation'))
            ->extraModalFooterActions(fn (Appointment $record, Action $action) => [
                $action->makeModalSubmitAction('confirmAndSend', arguments: ['send_notification' => true])
                    ->visible(fn () => NotificationTemplate::query()
                        ->where('type', NotificationType::AppointmentConfirmed)
                        ->whereHas('branches', fn (Builder $query) => $query->where('branches.id', $record->branch_id))
                        ->first()?->enable_mail ?? false)
                    ->label(__('Confirm with Notification'))
                    ->icon('heroicon-o-envelope')
                    ->color('primary'),
            ])
            ->action(function (Appointment $record, array $arguments) {
                if($record->confirmed) {
                    $record->confirmed_at = null;
                    $record->save();
                } else {
                    $record->markConfirmed($arguments['send_notification'] ?? false);
                    Notification::make()
                        ->title(__('Confirmed at :time', [
                            'time' => formatDateTime($record->confirmed_at),
                        ]))
                        ->success()
                        ->send();
                }
            });
    }
}
