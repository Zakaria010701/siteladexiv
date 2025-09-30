<?php

namespace App\Filament\Crm\Resources\Customers\RelationManagers;

use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use App\Filament\Crm\Resources\Appointments\Schemas\AppointmentTable;
use App\Models\Appointment;
use App\Models\AppointmentItem;
use App\Models\Payment;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AppointmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'appointments';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Appointments');
    }

    public static function getModelLabel(): string
    {
        return __('Appointment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Appointments');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('start')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('start')
            ->columns([
                TextColumn::make('start')
                    ->dateTime(getDateTimeFormat())
                    ->sortable(),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('appointmentItems')
                    ->badge()
                    ->formatStateUsing(fn (AppointmentItem $state) => $state->badge),
                TextColumn::make('appointmentOrder.gross_total')
                    ->label(__('Gross total'))
                    ->money('eur', locale: 'de'),
                TextColumn::make('appointmentOrder.status')
                    ->label(__('Payment'))
                    ->badge(),
                TextColumn::make('payments')
                    ->label(__('Payments'))
                    ->badge()
                    ->formatStateUsing(fn (Payment $state) => $state->badge),
            ])
            ->filters(AppointmentTable::filters())
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->url(fn (Appointment $record) => AppointmentResource::getUrl('edit', ['record' => $record])),
                    DeleteAction::make(),
                    ForceDeleteAction::make(),
                    RestoreAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
