<?php

namespace App\Filament\Crm\Resources\WorkTimeGroups;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Crm\Resources\WorkTimeGroups\RelationManagers\WorkTimesRelationManager;
use App\Filament\Crm\Resources\WorkTimeGroups\Pages\ListWorkTimeGroups;
use App\Filament\Crm\Resources\WorkTimeGroups\Pages\CreateWorkTimeGroup;
use App\Filament\Crm\Resources\WorkTimeGroups\Pages\EditWorkTimeGroup;
use App\Enums\TimeStep;
use App\Enums\WorkTimes\WorkTimeType;
use App\Filament\Crm\Resources\WorkTimeGroupResource\Pages;
use App\Models\WorkTimeGroup;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WorkTimeGroupResource extends Resource
{
    protected static ?string $model = WorkTimeGroup::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'Personal';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('branch_id')
                    ->relationship('branch', 'name')
                    ->required(),
                Select::make('room_id')
                    ->relationship('room', 'name')
                    ->required(),
                Select::make('type')
                    ->options(WorkTimeType::class)
                    ->required(),
                TimePicker::make('start')
                    ->required(),
                TimePicker::make('end')
                    ->required(),
                Select::make('repeat_step')
                    ->options(TimeStep::class)
                    ->required(),
                TextInput::make('repeat_every')
                    ->numeric()
                    ->required(),
                DatePicker::make('repeat_from')
                    ->default(today())
                    ->required(),
                DatePicker::make('repeat_till')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('branch.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('room.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('start'),
                TextColumn::make('end'),
                TextColumn::make('repeat_step')
                    ->searchable(),
                TextColumn::make('repeat_every')
                    ->searchable(),
                TextColumn::make('repeat_from')
                    ->date(getDateFormat())
                    ->sortable(),
                TextColumn::make('repeat_till')
                    ->date(getDateFormat())
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            WorkTimesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkTimeGroups::route('/'),
            'create' => CreateWorkTimeGroup::route('/create'),
            'edit' => EditWorkTimeGroup::route('/{record}/edit'),
        ];
    }
}
