<?php

namespace App\Filament\Crm\Resources\Customers\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Enums\Gender;
use App\Enums\Transactions\DiscountType;
use App\Models\Customer;
use App\Models\DiscountTemplate;
use App\Models\Service;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CustomPackagesRelationManager extends RelationManager
{
    protected static string $relationship = 'customPackages';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                TextInput::make('name')
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->maxLength(255),
                TextInput::make('short_code')
                    ->required()
                    ->disabled()
                    ->dehydrated(),
                Select::make('gender')
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->options(Gender::class),
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
                    ->suffix('€'),
                TextInput::make('default_discount')
                    ->disabled()
                    ->numeric()
                    ->suffix('€'),
                TextInput::make('price')
                    ->numeric()
                    ->suffix('€')
                    ->requiredWithoutAll(['discount_percentage', 'discount']),
                TextInput::make('discount_percentage')
                    ->label(__('Percentage'))
                    ->numeric()
                    ->requiredWithoutAll(['price', 'discount']),
                TextInput::make('discount')
                    ->numeric()
                    ->requiredWithoutAll(['discount_percentage', 'price']),
                TextInput::make('price')
                    ->numeric()
                    ->requiredWithoutAll(['discount_percentage', 'discount']),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('short_code'),
                TextColumn::make('category.name'),
                TextColumn::make('services.name')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->fillForm(function (CustomPackagesRelationManager $livewire) {
                        /** @var Customer $customer */
                        $customer = $livewire->getOwnerRecord();
                        $packageNumber = $customer->customPackages()->withTrashed()->count() + 1;

                        return [
                            'name' => sprintf('%s %s %s %s', $customer->firstname, $customer->lastname, __('Package'), $packageNumber),
                            'short_code' => sprintf(
                                '%s%sP %s',
                                substr($customer->firstname, 0, 1),
                                substr($customer->lastname, 0, 1),
                                $packageNumber
                            ),
                            'gender' => $customer->gender->value,
                        ];
                    }),
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
}
