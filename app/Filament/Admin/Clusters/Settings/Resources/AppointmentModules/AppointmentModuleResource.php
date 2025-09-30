<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\AppointmentModules;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use App\Filament\Admin\Clusters\Settings\Resources\AppointmentModules\Pages\ManageAppointmentModules;
use App\Enums\Appointments\AppointmentModule;
use App\Enums\Appointments\AppointmentType;
use App\Filament\Admin\Clusters\Settings\SettingsCluster;
use App\Filament\Admin\Clusters\Settings\Resources\AppointmentModuleResource\Pages;
use App\Models\AppointmentModuleSetting;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AppointmentModuleResource extends Resource
{
    protected static ?string $model = AppointmentModuleSetting::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = SettingsCluster::class;

    public static function getModelLabel(): string
    {
        return __('Appointment module');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Appointment modules');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('name')
                    ->options(AppointmentModule::class)
                    ->disabled()
                    ->required(),
                CheckboxList::make('appointment_types')
                    ->options(AppointmentType::class)
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
                TextColumn::make('name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
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
            'index' => ManageAppointmentModules::route('/'),
        ];
    }
}
