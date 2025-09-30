<?php

namespace App\Filament\Admin\Resources\Availabilities\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AvailabilityAbsencesRelationManager extends RelationManager
{
    protected static string $relationship = 'availabilityAbsences';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('start_date')
                    ->required()
                    ->minDate(fn () => $this->getOwnerRecord()->start_date)
                    ->maxDate(fn () => $this->getOwnerRecord()->end_date),
                DatePicker::make('end_date')
                    ->required()
                    ->minDate(fn () => $this->getOwnerRecord()->start_date)
                    ->maxDate(fn () => $this->getOwnerRecord()->end_date),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('start_date')
            ->columns([
                TextColumn::make('start_date')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->sortable(),
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
