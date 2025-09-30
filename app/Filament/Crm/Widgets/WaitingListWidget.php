<?php

namespace App\Filament\Crm\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Exception;
use Filament\Actions\DeleteAction;
use App\Actions\Appointments\BookAppointment;
use App\Enums\Appointments\AppointmentStatus;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use App\Filament\Crm\Resources\Customers\CustomerResource;
use App\Models\WaitingListEntry;
use App\Support\Appointment\BookingCalculator;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class WaitingListWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(WaitingListEntry::query())
            ->columns([
                TextColumn::make('branch.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('room.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('customer.full_name')
                    ->numeric()
                    ->url(fn (WaitingListEntry $record): string => isset($record->customer) ? CustomerResource::getUrl('edit', ['record' => $record->customer]) : '')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('appointment_type')
                    ->label(__('Type'))
                    ->badge(),
                TextColumn::make('note'),
                TextColumn::make('wish_date')
                    ->dateTime(getDateTimeFormat())
                    ->sortable(),
                TextColumn::make('wish_date_till')
                    ->dateTime(getDateTimeFormat())
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('book')
                    ->icon('heroicon-o-calendar')
                    ->schema(fn (WaitingListEntry $record) => [
                        Select::make('user_id')
                            ->live()
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('duration')
                            ->live()
                            ->integer(),
                        DatePicker::make('date')
                            ->native(false)
                            ->displayFormat(getDateFormat())
                            ->columnSpanFull()
                            ->default($record->wish_date)
                            ->prefixActions([
                                Action::make('subMonth')
                                    ->icon('heroicon-o-chevron-double-left')
                                    ->action(function ($state, Set $set) {
                                        $set('date', Carbon::parse($state)->subMonth()->toDateString());
                                    }),
                                Action::make('subWeek')
                                    ->icon('heroicon-o-chevron-left')
                                    ->action(function ($state, Set $set) {
                                        $set('date', Carbon::parse($state)->subWeek()->toDateString());
                                    }),
                                Action::make('subDay')
                                    ->icon('heroicon-o-minus')
                                    ->action(function ($state, Set $set) {
                                        $set('date', Carbon::parse($state)->subDay()->toDateString());
                                    }),
                            ])
                            ->suffixActions([
                                Action::make('addDay')
                                    ->icon('heroicon-o-plus')
                                    ->action(function ($state, Set $set) {
                                        $set('date', Carbon::parse($state)->addDay()->toDateString());
                                    }),
                                Action::make('addWeek')
                                    ->icon('heroicon-o-chevron-right')
                                    ->action(function ($state, Set $set) {
                                        $set('date', Carbon::parse($state)->addWeek()->toDateString());
                                    }),
                                Action::make('addMonth')
                                    ->icon('heroicon-o-chevron-double-right')
                                    ->action(function ($state, Set $set) {
                                        $set('date', Carbon::parse($state)->addMonth()->toDateString());
                                    }),
                            ])
                            ->live(),
                        ToggleButtons::make('time')
                            ->columnSpanFull()
                            ->options(function (Get $get) use ($record) {
                                return BookingCalculator::make(
                                    start: Carbon::parse($get('date'))->startOfDay(),
                                    end: Carbon::parse($get('date'))->endOfDay(),
                                    appointmentType: $record->appointment_type,
                                    branch: $record->branch,
                                    category: $record->category,
                                    services: $record->services,
                                    providers: !empty($get('user_id')) ? [$get('user_id')] : null,
                                    duration: !empty($get('duration')) ? $get('duration') : null,
                                )->openOptions();
                            } )
                            ->columns([
                                'default' => 2,
                                'sm' => 4,
                                'lg' => 6,
                            ])
                            //->hidden(fn (Get $get) => empty($get('date')) || empty($get('category_id')) || empty($get('services')))
                            ->required(),
                    ])
                    ->action(function (WaitingListEntry $record, array $data, array $arguments, Action $action) {
                        try {
                            $appointment = BookAppointment::make(
                                date: CarbonImmutable::parse($data['date'])->setTimeFromTimeString($data['time']),
                                appointmentType: $record->appointment_type,
                                branch: $record->branch,
                                category: $record->category,
                                services: $record->services,
                                customer: $record->customer,
                                providers: !empty($data['user_id']) ? [$data['user_id']] : null,
                                status: AppointmentStatus::Approved,
                            )->execute();
                        } catch (Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('status.result.error'))
                                ->send();
                            $action->halt(shouldRollBackDatabaseTransaction: true);
                        }

                        $record->delete();

                        $action->success();
                    }),
                DeleteAction::make(),
            ]);
    }
}
