<?php

namespace App\Filament\Admin\Clusters\AdminSettings\Resources\ModuleSettings;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use App\Filament\Admin\Clusters\AdminSettings\Resources\ModuleSettings\Pages\ListModuleSettings;
use App\Filament\Admin\Clusters\AdminSettings\Resources\ModuleSettings\Pages\CreateModuleSetting;
use App\Filament\Admin\Clusters\AdminSettings\Resources\ModuleSettings\Pages\EditModuleSetting;
use App\Filament\Admin\Clusters\AdminSettings\AdminSettingsCluster;
use App\Filament\Admin\Clusters\AdminSettings\Resources\ModuleSettingResource\Pages;
use App\Models\ModuleSetting;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class ModuleSettingResource extends Resource
{
    protected static ?string $model = ModuleSetting::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = AdminSettingsCluster::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->unique(ignoreRecord: true)
                    ->readOnly()
                    ->required(),
                Toggle::make('active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                ToggleColumn::make('active')
                    ->afterStateUpdated(function ($record, $state) {
                        Cache::forget('modules');
                    }),
            ])
            ->filters([
                //
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
            'index' => ListModuleSettings::route('/'),
            'create' => CreateModuleSetting::route('/create'),
            'edit' => EditModuleSetting::route('/{record}/edit'),
        ];
    }
}
