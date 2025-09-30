<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\Services;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Filament\Admin\Clusters\Settings\Resources\Services\Pages\ListServices;
use App\Filament\Admin\Clusters\Settings\Resources\Services\Pages\CreateService;
use App\Filament\Admin\Clusters\Settings\Resources\Services\Pages\EditService;
use App\Filament\Admin\Clusters\Settings\Resources\Services\Pages\ListServiceDependencies;
use App\Enums\TimeStep;
use App\Filament\Admin\Clusters\Settings\SettingsCluster;
use App\Filament\Admin\Clusters\Settings\Resources\ServiceResource\Pages;
use App\Models\Service;
use App\Models\SystemResourceType;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $cluster = SettingsCluster::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('Service');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Services');
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('short_code')
                    ->required()
                    ->maxLength(255),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('duration')
                    ->hint(__('Minutes'))
                    ->required()
                    ->numeric(),
                TextInput::make('customer_should_arrive_prior_to_appointment_minutes')
                    ->label(__('Customer should arrive early'))
                    ->hint(__('Minutes'))
                    ->required()
                    ->default(0)
                    ->numeric(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->postfix('€'),
                TextInput::make('next_appointment_in')
                    ->requiredWith('next_appointment_step')
                    ->columnStart(1)
                    ->integer(),
                Select::make('next_appointment_step')
                    ->requiredWith('next_appointment_in')
                    ->options(TimeStep::class),
                Fieldset::make('dependencies')
                    ->label(__('Dependencies'))
                    ->columns(3)
                    ->schema(function () {
                        $schema = [
                            CheckboxList::make('branches')
                                ->required()
                                ->columns(2)
                                ->bulkToggleable()
                                ->relationship('branches', 'name'),
                            CheckboxList::make('rooms')
                                ->required()
                                ->columns(2)
                                ->bulkToggleable()
                                ->relationship('rooms', 'name'),
                            CheckboxList::make('users')
                                ->required()
                                ->columns(2)
                                ->bulkToggleable()
                                ->relationship('users', 'name', modifyQueryUsing: fn (Builder $query) => $query->provider()),
                        ];

                        $types = SystemResourceType::all()
                            ->map(fn (SystemResourceType $type) => CheckboxList::make($type->name)
                                ->label($type->name)
                                ->columns(2)
                                ->bulkToggleable()
                                ->relationship(
                                    name:'systemResources',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: fn (Builder $query) => $query->where('system_resource_type_id', $type->id)
                                )
                            )
                            ->toArray();

                        $schema = array_merge($schema, $types);

                        return $schema;
                    }),
            ])
            ->columns(3);
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
                TextColumn::make('category.name')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('short_code')
                    ->searchable(),
                TextColumn::make('duration')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price')
                    ->money('EUR')
                    ->sortable(),
            ])
            ->defaultGroup('category.name')
            ->groups([
                Group::make('category.name')
                    ->label(__('Category'))
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->preload()
                    ->multiple(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton(),
                ActionGroup::make([
                    EditAction::make(),
                    ActionGroup::make([
                        DeleteAction::make(),
                        ForceDeleteAction::make(),
                        RestoreAction::make(),
                    ])->dropdown(false),
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
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/edit/{record}'),
            'dependencies' => ListServiceDependencies::route('/dependencies'),
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
