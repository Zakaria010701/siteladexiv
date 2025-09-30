<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\AppointmentExtras;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Clusters\Settings\Resources\AppointmentExtras\Pages\ManageAppointmentExtras;
use App\Enums\Appointments\AppointmentExtraType;
use App\Enums\Appointments\AppointmentType;
use App\Filament\Admin\Clusters\Settings\SettingsCluster;
use App\Filament\Admin\Clusters\Settings\Resources\AppointmentExtraResource\Pages;
use App\Models\AppointmentExtra;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class AppointmentExtraResource extends Resource
{
    protected static ?string $model = AppointmentExtra::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = SettingsCluster::class;

    public static function getModelLabel(): string
    {
        return __('Appointment Extra');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Appointment Extras');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Select::make('type')
                    ->live(onBlur: true)
                    ->options(AppointmentExtraType::class)
                    ->unique(ignoreRecord: true)
                    ->required(),
                Select::make('default')
                    ->hidden(fn (Get $get) => empty($get('type')?->options()))
                    ->options(fn (Get $get) => $get('type')?->options()),
                TextInput::make('default')
                    ->hidden(fn (Get $get) => ! $get('type')?->hasDefault())
                    ->numeric(),
                Toggle::make('is_required')
                    ->columnStart(1)
                    ->required(),
                Toggle::make('take_from_last_appointment')
                    ->required(),
                Toggle::make('split_per_service')
                    ->disabled(fn (Get $get) => ! $get('type')?->canSplitPerService())
                    ->required(),
                CheckboxList::make('appointment_types')
                    ->columnStart(1)
                    //->multiple()
                    ->options(AppointmentType::class)
                    ->required(),
                CheckboxList::make('treatmentTypes')
                    ->relationship('treatmentTypes', 'name'),
                CheckboxList::make('categories')
                    ->required()
                    ->relationship('categories', 'name'),
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
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('default')
                    ->searchable(),
                IconColumn::make('is_required')
                    ->boolean(),
                IconColumn::make('take_from_last_appointment')
                    ->boolean(),
                IconColumn::make('split_per_service')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->after(function (AppointmentExtra $record) {
                        Cache::forget('appointment_extras_lookup');
                    }),
                DeleteAction::make()
                    ->after(function (AppointmentExtra $record) {
                        Cache::forget('appointment_extras_lookup');
                    }),
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
            'index' => ManageAppointmentExtras::route('/'),
        ];
    }
}
