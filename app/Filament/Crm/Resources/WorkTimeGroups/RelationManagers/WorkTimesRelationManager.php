<?php

namespace App\Filament\Crm\Resources\WorkTimeGroups\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TimePicker;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Models\WorkTime;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class WorkTimesRelationManager extends RelationManager
{
    protected static string $relationship = 'workTimes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TimePicker::make('start')
                    ->required()
                    ->dehydrateStateUsing(fn (string $state, WorkTime $record): string => $record->start->setTimeFromTimeString($state)),
                TimePicker::make('end')
                    ->required()
                    ->dehydrateStateUsing(fn (string $state, WorkTime $record): string => $record->end->setTimeFromTimeString($state)),
            ]);
    }

    protected function configureEditAction(EditAction $action): void
    {
        $action
            ->authorize(static fn (RelationManager $livewire, Model $record): bool => (! $livewire->isReadOnly()) && $livewire->canEdit($record))
            ->schema(fn (Schema $schema): Schema => $this->form($schema->columns(2)));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('start')
            ->columns([
                TextColumn::make('start')
                    ->dateTime(getDateTimeFormat()),
                TextColumn::make('end')
                    ->dateTime(getDateTimeFormat()),
                TextColumn::make('room.name'),

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
