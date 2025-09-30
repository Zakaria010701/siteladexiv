<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\ServicePackageDurations;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Clusters\Settings\Resources\ServicePackageDurations\Pages\ListServicePackageDurations;
use App\Filament\Admin\Clusters\Settings\Resources\ServicePackageDurations\Pages\CreateServicePackageDuration;
use App\Filament\Admin\Clusters\Settings\Resources\ServicePackageDurations\Pages\EditServicePackageDuration;
use App\Filament\Admin\Clusters\Settings\SettingsCluster;
use App\Filament\Admin\Clusters\Settings\Resources\ServicePackageDurationResource\Pages;
use App\Models\ServicePackageDuration;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServicePackageDurationResource extends Resource
{
    protected static ?string $model = ServicePackageDuration::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = SettingsCluster::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('count')
                    ->integer()
                    ->required(),
                TextInput::make('percentage')
                    ->integer()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('count'),
                TextColumn::make('percentage'),
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
            'index' => ListServicePackageDurations::route('/'),
            'create' => CreateServicePackageDuration::route('/create'),
            'edit' => EditServicePackageDuration::route('/{record}/edit'),
        ];
    }
}
