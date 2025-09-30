<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\EnergySettings;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Clusters\Settings\Resources\EnergySettings\Pages\ManageEnergySettings;
use App\Enums\Appointments\AppointmentExtraType;
use App\Enums\Appointments\Extras\HairType;
use App\Enums\Appointments\Extras\PigmentType;
use App\Filament\Admin\Clusters\Settings\SettingsCluster;
use App\Filament\Admin\Clusters\Settings\Resources\EnergySettingResource\Pages;
use App\Models\EnergySetting;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EnergySettingResource extends Resource
{
    protected static ?string $model = EnergySetting::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = SettingsCluster::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('treatment_type_id')
                    ->live(onBlur: true)
                    ->relationship('treatmentType', 'name')
                    ->required(),
                Select::make('hair_type')
                    ->options(HairType::class),
                Select::make('pigment_type')
                    ->options(PigmentType::class),
                Select::make('skin_type')
                    ->options(AppointmentExtraType::SkinType->options()),
                Select::make('spot_size')
                    ->options(fn (Get $get) => AppointmentExtraType::getSpotSizeOptions($get('treatment_type_id'))),
                TextInput::make('energy')
                    ->required()
                    ->numeric(),
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
                TextColumn::make('treatmentType.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('hair_type')
                    ->searchable(),
                TextColumn::make('pigment_type')
                    ->searchable(),
                TextColumn::make('skin_type')
                    ->searchable(),
                TextColumn::make('spot_size')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('energy')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => ManageEnergySettings::route('/'),
        ];
    }
}
