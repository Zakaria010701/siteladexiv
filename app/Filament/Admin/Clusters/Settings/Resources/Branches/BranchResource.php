<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\Branches;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Filament\Admin\Clusters\Settings\Resources\Branches\RelationManagers\AvailabilitiesRelationManager;
use App\Filament\Admin\Clusters\Settings\Resources\Branches\Pages\ListBranches;
use App\Filament\Admin\Clusters\Settings\Resources\Branches\Pages\CreateBranch;
use App\Filament\Admin\Clusters\Settings\Resources\Branches\Pages\EditBranch;
use App\Filament\Admin\Clusters\Settings\SettingsCluster;
use App\Filament\Admin\Clusters\Settings\Resources\BranchResource\Pages;
use App\Filament\Admin\Clusters\Settings\Resources\BranchResource\RelationManagers;
use App\Models\Branch;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $cluster = SettingsCluster::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('Branch');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Branches');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('short_code')
                    ->required()
                    ->maxLength(255),
                Fieldset::make(__('Open times'))
                    ->schema([
                        TextInput::make('calendar_start_time')
                            ->required(),
                        TextInput::make('calendar_end_time')
                            ->required(),
                        TextInput::make('frontend_start_time')
                            ->required(),
                        TextInput::make('frontend_end_time')
                            ->required(),
                    ]),
                CheckboxList::make('open_days')
                    ->required()
                    ->options([
                        '0' => __('Sunday'),
                        '1' => __('Monday'),
                        '2' => __('Tuesday'),
                        '3' => __('Wednesday'),
                        '4' => __('Thursday'),
                        '5' => __('Friday'),
                        '6' => __('Saturday'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('short_code')
                    ->searchable(),
                TextColumn::make('calendar_start_time'),
                TextColumn::make('calendar_end_time'),
                TextColumn::make('frontend_start_time'),
                TextColumn::make('frontend_end_time'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AvailabilitiesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBranches::route('/'),
            'create' => CreateBranch::route('/create'),
            'edit' => EditBranch::route('/edit/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
