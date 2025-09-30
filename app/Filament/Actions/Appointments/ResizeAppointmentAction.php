<?php

namespace App\Filament\Actions\Appointments;

use Filament\Schemas\Components\Utilities\Get;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use App\Models\Appointment;
use App\Models\Room;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Livewire\Component;

class ResizeAppointmentAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'resizeAppointment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Change duration'));

        $this->authorize(fn (Component $livewire) => AppointmentResource::canEdit($livewire->getRecord()));

        $this->record(fn (Component $livewire) => $livewire->getRecord());

        $this->modalHeading(__('Change Appointment duration'));
        $this->modalIcon('heroicon-o-arrows-up-down');
        $this->modalDescription(__('filament-actions::modal.confirmation'));

        $this->fillForm(fn (array $arguments) => [
            'start' => $arguments['start'],
            'end' => $arguments['end'],
            'room_id' => $arguments['room_id'],
        ]);

        $this->schema([
            DateTimePicker::make('start')
                ->required()
                ->disabled()
                ->dehydrated(),
            TextInput::make('end')
                ->label(__('Duration'))
                ->required()
                ->disabled()
                ->dehydrated()
                ->numeric()
                ->formatStateUsing(fn ($state, Get $get): int => isset($state) ? Carbon::parse($get('start'))->diffInMinutes($state) : 0)
                ->dehydrateStateUsing(fn ($state, Get $get): Carbon => Carbon::parse($get('start'))->addMinutes($state)),
            Select::make('room_id')
                ->label(__('Room'))
                ->options(Room::all()->pluck('name', 'id'))
                ->required()
                ->disabled()
                ->dehydrated(),
        ]);

        $this->action(function (array $data, array $arguments, Appointment $record) {
            $old = clone $record;

            $record->start = $data['start'];
            $record->end = $data['end'];
            $record->save();

            Notification::make()
                ->title(__('status.result.success'))
                ->success()
                ->send();
        });
    }
}
