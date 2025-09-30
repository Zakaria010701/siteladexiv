<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\DiscountTemplates;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Filament\Admin\Clusters\Settings\Resources\DiscountTemplates\Pages\ManageDiscountTemplates;
use App\Enums\Transactions\DiscountType;
use App\Filament\Admin\Clusters\Settings\SettingsCluster;
use App\Filament\Admin\Clusters\Settings\Resources\DiscountTemplateResource\Pages;
use App\Models\DiscountTemplate;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Unique;

class DiscountTemplateResource extends Resource
{
    protected static ?string $model = DiscountTemplate::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = SettingsCluster::class;

    public static function getModelLabel(): string
    {
        return __('Discount template');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Discount templates');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->options(DiscountType::class)
                    ->default(DiscountType::Package->value)
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('type', $get('type'))
                    ),
                TextInput::make('percentage')
                    ->requiredWithout('amount')
                    ->suffix('%')
                    ->numeric(),
                TextInput::make('amount')
                    ->requiredWithout('percentage')
                    ->suffix('€')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type'),
                TextColumn::make('quantity'),
                TextColumn::make('percentage'),
                TextColumn::make('amount'),
            ])
            ->defaultGroup('type')
            ->groups([
                Group::make('type')
                    ->label(__('Type'))
                    ->titlePrefixedWithLabel(false),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDiscountTemplates::route('/'),
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
