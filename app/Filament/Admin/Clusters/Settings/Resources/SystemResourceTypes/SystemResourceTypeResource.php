<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\SystemResourceTypes;

use App\Filament\Admin\Clusters\Settings\Resources\SystemResourceTypes\SystemResourceTypeResource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Filament\Admin\Clusters\Settings\Resources\SystemResourceTypes\Pages\ListSystemResourceTypes;
use App\Filament\Admin\Clusters\Settings\Resources\SystemResourceTypes\Pages\CreateSystemResourceType;
use App\Filament\Admin\Clusters\Settings\Resources\SystemResourceTypes\Pages\EditSystemResourceType;
use App\Filament\Admin\Clusters\Settings\Resources\SystemResourceTypes\Pages\ManageSystemResource;
use App\Enums\Resources\ResourceFieldType;
use App\Filament\Admin\Clusters\Settings\SettingsCluster;
use App\Filament\Admin\Clusters\Settings\Resources\SystemResourceTypeResource\Pages;
use App\Filament\Admin\Clusters\Settings\Resources\SystemResourceTypeResource\RelationManagers;
use App\Forms\Components\TableRepeater;
use App\Forms\Components\TableRepeater\Header;
use App\Models\SystemResourceType;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SystemResourceTypeResource extends Resource
{
    protected static ?string $model = SystemResourceType::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = SettingsCluster::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Fieldset::make('appointment')
                    ->label(__('Appointment'))
                    ->columns(3)
                    ->schema([
                        Toggle::make('show_in_appointment')
                            ->inline(false),
                        Toggle::make('is_required')
                            ->inline(false),
                        Toggle::make('allow_multiple')
                            ->inline(false),
                    ]),

                Fieldset::make('dependencies')
                    ->label(__('Dependencies'))
                    ->columns(3)
                    ->schema([
                        Toggle::make('depends_on_branch')
                            ->inline(false),
                        Toggle::make('depends_on_room')
                            ->inline(false),
                        Toggle::make('depends_on_category')
                            ->inline(false),
                        Toggle::make('depends_on_user')
                            ->inline(false),
                        Toggle::make('depends_on_availability')
                            ->inline(false),
                        Select::make('system_resource_type_dependencies')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->relationship('systemResourceTypeDependencies', 'name', ignoreRecord: true),
                    ]),
                Repeater::make('fields')
                    ->relationship('resourceFields')
                    ->reorderable()
                    ->collapsible()
                    ->orderColumn('order')
                    ->columnSpanFull()
                    ->columns(4)
                    ->schema([
                        Select::make('type')
                            ->required()
                            ->live()
                            ->options(ResourceFieldType::class),
                        TextInput::make('name')
                            ->required()
                            ->rules(['lowercase'])
                            ->alphaDash()
                            ->maxLength(255),
                        TextInput::make('label')
                            ->required()
                            ->maxLength(255),
                        Toggle::make('required')
                            ->inline(false),
                        Toggle::make('disabled')
                            ->inline(false),
                        TableRepeater::make('options')
                            ->reorderable(false)
                            ->visible(fn (Get $get) => $get('type')?->hasOptions() ?? false)
                            ->columnSpan(2)
                            ->headers([
                                Header::make('value')
                                    ->label(__('Value'))
                                    ->markAsRequired(),
                                Header::make('label')
                                    ->label(__('Label'))
                                    ->markAsRequired(),
                            ])
                            ->schema([
                                TextInput::make('value')
                                    ->required()
                                    ->rules(['lowercase'])
                                    ->alphaDash()
                                    ->maxLength(255),
                                TextInput::make('label')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ]),
                KeyValue::make('meta')
                    ->columnSpanFull()
                    ->visible(auth()->user()->can('admin_system_resource_type')),
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
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->searchable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('index_resources')
                        ->label(__('Resources'))
                        ->url(fn (SystemResourceType $record) => SystemResourceTypeResource::getUrl('resources', ['record' => $record])),
                    DeleteAction::make(),
                    RestoreAction::make(),
                ]),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSystemResourceTypes::route('/'),
            'create' => CreateSystemResourceType::route('/create'),
            'edit' => EditSystemResourceType::route('/{record}/edit'),
            'resources' => ManageSystemResource::route('/{record}/resources'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            EditSystemResourceType::class,
            ManageSystemResource::class,
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
