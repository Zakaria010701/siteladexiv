<?php

namespace App\Filament\Cms\Resources\CmsMenuItems;

use App\Filament\Cms\Resources\CmsMenuItems\Pages\CreateCmsMenuItem;
use App\Filament\Cms\Resources\CmsMenuItems\Pages\EditCmsMenuItem;
use App\Filament\Cms\Resources\CmsMenuItems\Pages\ListCmsMenuItems;
use App\Filament\Cms\Resources\CmsMenuItems\Schemas\CmsMenuItemForm;
use App\Filament\Cms\Resources\CmsMenuItems\Tables\CmsMenuItemsTable;
use App\Models\CmsMenuItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CmsMenuItemResource extends Resource
{
    protected static ?string $model = CmsMenuItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return CmsMenuItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CmsMenuItemsTable::configure($table);
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
            'index' => ListCmsMenuItems::route('/'),
            'create' => CreateCmsMenuItem::route('/create'),
            'edit' => EditCmsMenuItem::route('/{record}/edit'),
        ];
    }
}
