<?php

namespace App\Filament\Admin\Resources\Availabilities\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Models\Availability;
use App\Models\AvailabilityException;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AvailabilityExceptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'availabilityExceptions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->required()
                    ->minDate(fn () => $this->getOwnerRecord()->start_date)
                    ->maxDate(fn () => $this->getOwnerRecord()->end_date),
                Select::make('availability_type_id')
                    ->relationship('availabilityType', 'name')
                    ->searchable()
                    ->preload(),
                TimePicker::make('start'),
                TimePicker::make('target_minutes')
                    ->required()
                    ->dehydrateStateUsing(fn (string $state) => deformatTime($state))
                    ->formatStateUsing(fn (?int $state) => formatTime($state ?? 0)),
                Select::make('room_id')
                    ->relationship('room', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                TextColumn::make('date')
                    ->sortable()
                    ->searchable()
                    ->date(getDateFormat()),
                TextColumn::make('start')
                    ->date(getTimeFormat()),
                TextColumn::make('target_minutes')
                    ->formatStateUsing(fn (int $state) => formatTime($state)),
                TextColumn::make('room.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('availabilityType.name')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn (AvailabilityException $record) => Color::generateV3Palette($record->availabilityType->color)),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
