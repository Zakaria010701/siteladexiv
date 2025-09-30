<?php

namespace App\Livewire\Forms;

use Filament\Schemas\Components\Utilities\Get;
use App\Enums\Appointments\AppointmentMoveReason;
use App\Events\Appointments\AppointmentMovedEvent;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use App\Models\Appointment;
use App\Models\Room;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class MoveAppointmentAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'moveAppointment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Move'));

        $this->authorize(fn (Component $livewire) => AppointmentResource::canEdit($livewire->getRecord()));

        $this->record(fn (Component $livewire) => $livewire->getRecord());

        $this->modalHeading(__('Move Appointment'));

        $this->modalIcon('heroicon-o-arrows-pointing-out');
        $this->modalDescription(__('filament-actions::modal.confirmation'));
        $this->modalSubmitActionLabel(__('Move without Notification'));

        $this->fillForm(fn (array $arguments) => [
            'start' => $arguments['start'],
            'end' => $arguments['end'],
            'room_id' => $arguments['room_id'],
        ]);

        $this->schema([
            Placeholder::make('prefered_providers')
                ->label(__('Prefered providers'))
                ->visible(fn (Appointment $record) => $record->customer?->preferedProviders()->exists() ?? false)
                ->content(function (Appointment $record) {
                    $providers = $record->customer->preferedProviders;
                    $text = $providers->implode('name', ', ');
                    return new HtmlString("<span class=\"text-red-500\">$text</span>");
                }),
            DateTimePicker::make('start')
                ->required()
                ->disabled()
                ->dehydrated(),
            DateTimePicker::make('end')
                ->required()
                ->disabled()
                ->dehydrated(),
            Select::make('room_id')
                ->label(__('Room'))
                ->options(Room::all()->pluck('name', 'id'))
                ->required()
                ->disabled()
                ->dehydrated(),
            Select::make('movement_reason')
                ->live()
                ->required()
                ->options(AppointmentMoveReason::class),
            Textarea::make('movement_note')
                ->required(fn (Get $get) => $get('movement_reason') == AppointmentMoveReason::Other->value),
        ]);

        $this->extraModalFooterActions(fn (Action $action) => [
            $action->makeModalSubmitAction('moveAndSend', arguments: ['send_notification' => true])
                ->label(__('Move with Notification'))
                ->icon('heroicon-o-envelope')
                ->color('primary'),
        ]);

        $this->action(function (array $data, array $arguments, Appointment $record) {
            $old = clone $record;

            $record->start = $data['start'];
            $record->end = $data['end'];
            $record->room_id = $data['room_id'];
            $record->save();

            $reason = AppointmentMoveReason::from($data['movement_reason']);
            AppointmentMovedEvent::dispatch($record, auth()->user(), $reason, $arguments['send_notification'] ?? false);

            Notification::make()
                ->title(__('Moved successfully'))
                ->success()
                ->send();
        });
    }
}
