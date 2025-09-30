<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\ServicePackages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Filament\Admin\Clusters\Settings\Resources\ServicePackages\Pages\ManageServicePackages;
use App\Enums\Gender;
use App\Enums\Transactions\DiscountType;
use App\Filament\Admin\Clusters\Settings\SettingsCluster;
use App\Filament\Admin\Clusters\Settings\Resources\ServicePackageResource\Pages;
use App\Filament\Crm\Resources\Customers\CustomerResource;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\DiscountTemplate;
use App\Models\Service;
use App\Models\ServicePackage;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServicePackageResource extends Resource
{
    protected static ?string $model = ServicePackage::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = SettingsCluster::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('Service package');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Service packages');
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
                Select::make('gender')
                    ->options(Gender::class)
                    ->required(),
                Select::make('category_id')
                    ->live(onBlur: true)
                    ->relationship('category', 'name')
                    ->required(),
                Select::make('services')
                    ->live(onBlur: true)
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->relationship('services')
                    ->options(fn (Get $get) => Service::query()
                        ->where('category_id', $get('category_id'))
                        ->pluck('name', 'id')
                    )
                    ->columnSpan(2)
                    ->afterStateUpdated(function (array $state, Set $set) {
                        $services = Service::query()->whereIn('id', $state)->get();
                        $set('default_price', $services->sum('price'));
                        $template = DiscountTemplate::query()
                            ->where('type', DiscountType::Package)
                            ->where('quantity', '<=', $services->count())
                            ->orderByDesc('quantity')
                            ->first();
                        $set('default_discount_percentage', $template?->percentage);
                        $set('default_discount', $template?->amount);
                    })
                    ->afterStateHydrated(function (array $state, Set $set) {
                        $services = Service::query()->whereIn('id', $state)->get();
                        $set('default_price', $services->sum('price'));
                        $template = DiscountTemplate::query()
                            ->where('type', DiscountType::Package)
                            ->where('quantity', '<=', $services->count())
                            ->orderByDesc('quantity')
                            ->first();
                        $set('default_discount_percentage', $template?->percentage);
                        $set('default_discount', $template?->amount);
                    }),
                TextInput::make('default_price')
                    ->disabled()
                    ->numeric()
                    ->suffix('€'),
                TextInput::make('default_discount_percentage')
                    ->disabled()
                    ->numeric()
                    ->suffix('%'),
                TextInput::make('default_discount')
                    ->disabled()
                    ->numeric()
                    ->suffix('€'),
                TextInput::make('price')
                    ->numeric()
                    ->suffix('€'),
                TextInput::make('discount_percentage')
                    ->label(__('Percentage'))
                    ->suffix('%')
                    ->numeric(),
                TextInput::make('discount')
                    ->suffix('€')
                    ->numeric(),

            ])
            ->columns(3);
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
                TextColumn::make('short_code')
                    ->searchable(),
                TextColumn::make('gender')
                    ->sortable(),
                TextColumn::make('category.name')
                    ->sortable(),
                TextColumn::make('services.name')
                    ->badge(),
                TextColumn::make('customer.full_name')
                    ->url(fn (ServicePackage $record): string => isset($record->customer) ? CustomerResource::getUrl('edit', ['record' => $record->customer], panel: 'crm') : '')
                    ->sortable()
                    ->searchable(['firstname', 'lastname']),
                TextColumn::make('discount_percentage')
                    ->toggledHiddenByDefault()
                    ->formatStateUsing(fn ($state) => $state ? $state . '%' : null)
                    ->sortable(),
                TextColumn::make('discount')
                    ->toggledHiddenByDefault()
                    ->formatStateUsing(fn ($state) => formatMoney($state))
                    ->sortable(),
                TextColumn::make('price')
                    ->formatStateUsing(fn ($state) => formatMoney($state))
                    ->sortable(),

            ])
            ->defaultGroup('gender')
            ->groups([
                Group::make('category.name')
                    ->label(__('Category'))
                    ->titlePrefixedWithLabel(false),
                Group::make('gender')
                    ->label(__('Gender'))
                    ->titlePrefixedWithLabel(false),
                Group::make('customer_id')
                    ->label(__('Customer'))
                    ->getTitleFromRecordUsing(fn (ServicePackage $record): string => $record->customer?->full_name ?? __('Universal'))
                    ->titlePrefixedWithLabel(false)
            ])
            ->filters([
                SelectFilter::make('customer_id')
                    ->label(__('Customer'))
                    ->searchable(['firstname', 'lastname'])
                    ->getOptionLabelFromRecordUsing(fn (Customer $record) => $record->full_name)
                    ->relationship('customer', 'lastname'),
                SelectFilter::make('gender')
                    ->options(Gender::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    ForceDeleteAction::make(),
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
            'index' => ManageServicePackages::route('/'),
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
