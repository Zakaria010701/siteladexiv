<?php

namespace App\Livewire\Appointments;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Fieldset;
use App\Enums\Notifications\NotificationType;
use App\Filament\Widgets\Concerns\Calendar\InteractsWithModalActions;
use App\Models\Appointment;
use App\Models\NotificationTemplate;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Component;

//use Filament\Actions\Action;

class AppointmentInfo extends Component implements HasActions, HasForms, HasInfolists
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithInfolists;
    use InteractsWithModalActions;

    public ?array $data = [];

    public Appointment $appointment;

    public function render()
    {
        return view('livewire.appointments.appointment-info');
    }

    public function mount(Appointment $appointment): void
    {
        $this->appointment = $appointment;
        $this->form->fill();
    }

    public function appointmentInfoList(Schema $schema): Schema
    {
        return $schema
            ->record($this->appointment)
            ->columns(3)
            ->components([
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
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model($this->appointment)
            ->statePath('data')
            ->components([
                Toggle::make('check_in_at')
                    ->live()
                    ->label(fn (Appointment $record) => $record->checked_in ? __('Checked in at :time', [
                        'time' => formatDateTime($record->check_in_at),
                    ]) : __('Check in'))
                    ->formatStateUsing(fn (Appointment $record) => $record->checked_in)
                    ->afterStateUpdated(fn () => $this->mountAction('checkIn')),
                Toggle::make('check_out_at')
                    ->live()
                    ->label(fn () => $this->appointment->checked_out ? __('Checked out at :time', [
                        'time' => formatDateTime($this->appointment->check_out_at),
                    ]) : __('Check out'))
                    ->formatStateUsing(fn (Appointment $record) => $record->checked_out)
                    ->afterStateUpdated(fn () => $this->mountAction('checkOut')),
                Toggle::make('controlled_at')
                    ->live()
                    ->label(fn () => $this->appointment->controlled ? __('Controlled at :time', [
                        'time' => formatDateTime($this->appointment->controlled_at),
                    ]) : __('Verification'))
                    ->formatStateUsing(fn (Appointment $record) => $record->controlled)
                    ->afterStateUpdated(fn () => $this->mountAction('control')),
                Toggle::make('confirmed_at')
                    ->live()
                    ->label(fn () => $this->appointment->confirmed ? __('Confirmed at :time', [
                        'time' => formatDateTime($this->appointment->confirmed_at),
                    ]) : __('Confirm'))
                    ->formatStateUsing(fn (Appointment $record) => $record->confirmed)
                    ->afterStateUpdated(fn () => $this->mountAction('confirm')),
            ]);
    }

    public function checkInAction(): Action
    {
        return Action::make('checkIn')
            ->label(fn () => $this->appointment->checked_in ? __('Checked in at :time', [
                'time' => formatDateTime($this->appointment->check_in_at),
            ]) : __('Check in'))
            ->modalDescription(__('filament-actions::modal.confirmation'))
            ->extraModalFooterActions(fn (Action $action) => [
                $action->makeModalSubmitAction('checkInAndSend', arguments: ['send_notification' => true])
                    ->visible(fn () => NotificationTemplate::query()
                        ->where('type', NotificationType::AppointmentCheckIn)
                        ->whereHas('branches', fn (Builder $query) => $query->where('branches.id', $this->appointment->branch_id))
                        ->first()?->enable_mail ?? false)
                    ->label(__('Check in with Notification'))
                    ->icon('heroicon-o-envelope')
                    ->color('primary'),
            ])
            ->action(function (array $arguments) {
                if($this->appointment->checked_in) {
                    $this->appointment->check_in_at = null;
                    $this->appointment->save();
                } else {
                    $this->appointment->markCheckedIn($arguments['send_notification'] ?? false);
                    Notification::make()
                        ->title(__('Checked in at :time', [
                            'time' => formatDateTime($this->appointment->check_in_at),
                        ]))
                        ->success()
                        ->send();
                }
            });
    }

    public function checkOutAction(): Action
    {
        return Action::make('checkOut')
            ->label(fn () => $this->appointment->checked_out ? __('Checked out at :time', [
                'time' => formatDateTime($this->appointment->check_out_at),
            ]) : __('Check out'))
            ->modalDescription(__('filament-actions::modal.confirmation'))
            ->extraModalFooterActions(fn (Action $action) => [
                $action->makeModalSubmitAction('checkOutAndSend', arguments: ['send_notification' => true])
                    ->visible(fn () => NotificationTemplate::query()
                        ->where('type', NotificationType::AppointmentCheckOut)
                        ->whereHas('branches', fn (Builder $query) => $query->where('branches.id', $this->appointment->branch_id))
                        ->first()?->enable_mail ?? false)
                    ->label(__('Check out with Notification'))
                    ->icon('heroicon-o-envelope')
                    ->color('primary'),
            ])
            ->action(function (array $arguments) {
                if($this->appointment->checked_out) {
                    $this->appointment->check_out_at = null;
                    $this->appointment->save();
                } else {
                    $this->appointment->markCheckedOut($arguments['send_notification'] ?? false);
                    Notification::make()
                        ->title(__('Check out at :time', [
                            'time' => formatDateTime($this->appointment->check_out_at),
                        ]))
                        ->success()
                        ->send();
                }
            });
    }

    public function controlAction(): Action
    {
        return Action::make('control')
            ->label(fn () => $this->appointment->controlled ? __('Controlled at :time', [
                'time' => formatDateTime($this->appointment->controlled_at),
            ]) : __('Verification'))
            ->modalDescription(__('filament-actions::modal.confirmation'))
            ->extraModalFooterActions(fn (Action $action) => [
                $action->makeModalSubmitAction('controllAndSend', arguments: ['send_notification' => true])
                    ->visible(fn () => NotificationTemplate::query()
                        ->where('type', NotificationType::AppointmentControlled)
                        ->whereHas('branches', fn (Builder $query) => $query->where('branches.id', $this->appointment->branch_id))
                        ->first()?->enable_mail ?? false)
                    ->label(__('Controll with Notification'))
                    ->icon('heroicon-o-envelope')
                    ->color('primary'),
            ])
            ->action(function (array $arguments) {
                if($this->appointment->controlled) {
                    $this->appointment->controlled_at = null;
                    $this->appointment->save();
                } else {
                    $this->appointment->markControlled($arguments['send_notification'] ?? false);
                    Notification::make()
                        ->title(__('Controlled at :time', [
                            'time' => formatDateTime($this->appointment->controlled_at),
                        ]))
                        ->success()
                        ->send();
                }
            });
    }

    public function confirmAction(): Action
    {
        return Action::make('confirm')
            ->label(fn () => $this->appointment->confirmed ? __('Confirmed at :time', [
                'time' => formatDateTime($this->appointment->confirmed_at),
            ]) : __('Confirm'))
            ->modalDescription(__('filament-actions::modal.confirmation'))
            ->extraModalFooterActions(fn (Action $action) => [
                $action->makeModalSubmitAction('confirmAndSend', arguments: ['send_notification' => true])
                    ->visible(fn () => NotificationTemplate::query()
                        ->where('type', NotificationType::AppointmentConfirmed)
                        ->whereHas('branches', fn (Builder $query) => $query->where('branches.id', $this->appointment->branch_id))
                        ->first()?->enable_mail ?? false)
                    ->label(__('Confirm with Notification'))
                    ->icon('heroicon-o-envelope')
                    ->color('primary'),
            ])
            ->action(function (array $arguments) {
                if($this->appointment->confirmed) {
                    $this->appointment->confirmed_at = null;
                    $this->appointment->save();
                } else {
                    $this->appointment->markConfirmed($arguments['send_notification'] ?? false);
                    Notification::make()
                        ->title(__('Confirmed at :time', [
                            'time' => formatDateTime($this->appointment->confirmed_at),
                        ]))
                        ->success()
                        ->send();
                }
            });
    }
}
