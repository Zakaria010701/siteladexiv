<?php

namespace App\Filament\Cms\Resources\CmsPages;

use App\Filament\Cms\Resources\CmsPages\Pages\CreateCmsPage;
use App\Filament\Cms\Resources\CmsPages\Pages\EditCmsPage;
use App\Filament\Cms\Resources\CmsPages\Pages\ListCmsPages;
use App\Filament\Cms\Resources\CmsPages\Pages\ViewCmsPage;
use App\Filament\Cms\Resources\CmsPages\Schemas\CmsPageForm;
use App\Filament\Cms\Resources\CmsPages\Schemas\CmsPageInfolist;
use App\Filament\Cms\Resources\CmsPages\Tables\CmsPagesTable;
use App\Models\CmsPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CmsPageResource extends Resource
{
    protected static ?string $model = CmsPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return CmsPageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CmsPageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CmsPagesTable::configure($table);
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
            'index' => ListCmsPages::route('/'),
            'create' => CreateCmsPage::route('/create'),
            'view' => ViewCmsPage::route('/{record}'),
            'edit' => EditCmsPage::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
