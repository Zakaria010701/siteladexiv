<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\AppointmentComplaintTypes;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Clusters\Settings\Resources\AppointmentComplaintTypes\Pages\ManageAppointmentComplaintTypes;
use App\Filament\Admin\Clusters\Settings\SettingsCluster;
use App\Filament\Admin\Clusters\Settings\Resources\AppointmentComplaintTypeResource\Pages;
use App\Filament\Admin\Clusters\Settings\Resources\AppointmentComplaintTypeResource\RelationManagers;
use App\Models\AppointmentComplaintType;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AppointmentComplaintTypeResource extends Resource
{
    protected static ?string $model = AppointmentComplaintType::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = SettingsCluster::class;

    public static function getModelLabel(): string
    {
        return __('Complaint Type');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Complaint Types');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->unique(ignoreRecord: true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->numeric(),
                TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime(getDateTimeFormat()),
                TextColumn::make('updated_at')
                    ->sortable()
                    ->dateTime(getDateTimeFormat()),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
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
            'index' => ManageAppointmentComplaintTypes::route('/'),
        ];
    }
}
