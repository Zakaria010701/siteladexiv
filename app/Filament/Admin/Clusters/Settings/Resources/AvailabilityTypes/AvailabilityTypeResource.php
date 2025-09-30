<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\AvailabilityTypes;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Clusters\Settings\Resources\AvailabilityTypes\Pages\ListAvailabilityTypes;
use App\Filament\Admin\Clusters\Settings\Resources\AvailabilityTypes\Pages\CreateAvailabilityType;
use App\Filament\Admin\Clusters\Settings\Resources\AvailabilityTypes\Pages\EditAvailabilityType;
use App\Filament\Admin\Clusters\Settings\SettingsCluster;
use App\Filament\Admin\Clusters\Settings\Resources\AvailabilityTypeResource\Pages;
use App\Filament\Admin\Clusters\Settings\Resources\AvailabilityTypeResource\RelationManagers;
use App\Models\AvailabilityType;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AvailabilityTypeResource extends Resource
{
    protected static ?string $model = AvailabilityType::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = SettingsCluster::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                ColorPicker::make('color')
                    ->helperText(__('The color of the events in the calendar'))
                    ->required()
                    ->default('#a0a0a0'),
                Toggle::make('is_hidden')
                    ->live()
                    ->helperText(__('Hide the events from the calendar'))
                    ->inline(false),
                Toggle::make('is_all_day')
                    ->helperText(__('Enable to display the events in the top part of the calendar'))
                    ->visible(fn (Get $get) => !$get('is_hidden'))
                    ->default(true)
                    ->inline(false),
                Toggle::make('is_background')
                    ->helperText(__('Enable to block the calendar during this events time'))
                    ->visible(fn (Get $get) => !$get('is_hidden'))
                    ->live()
                    ->default(false)
                    ->inline(false),
                Toggle::make('is_background_inverted')
                    ->helperText(__('Enable to open the calendar during this events time'))
                    ->visible(fn (Get $get) => $get('is_background') && !$get('is_hidden'))
                    ->default(false)
                    ->inline(false),
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
                TextColumn::make('name')
                    ->searchable(),
                ColorColumn::make('color'),
                IconColumn::make('is_hidden')
                    ->boolean(),
                IconColumn::make('is_all_day')
                    ->boolean(),
                IconColumn::make('is_background')
                    ->boolean(),
                IconColumn::make('is_background_inverted')
                    ->boolean(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAvailabilityTypes::route('/'),
            'create' => CreateAvailabilityType::route('/create'),
            'edit' => EditAvailabilityType::route('/{record}/edit'),
        ];
    }
}
