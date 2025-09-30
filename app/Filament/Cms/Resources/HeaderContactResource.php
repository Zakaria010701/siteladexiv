<?php

namespace App\Filament\Cms\Resources;

use App\Filament\Cms\Resources\HeaderContactResource\Pages;
use App\Filament\Cms\Resources\HeaderContactResource\Schemas\HeaderContactForm;
use App\Filament\Cms\Resources\HeaderContactResource\Tables\HeaderContactTable;
use App\Models\HeaderContact;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HeaderContactResource extends Resource
{
    protected static ?string $model = HeaderContact::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;

    protected static ?string $navigationLabel = 'Header Contact';

    protected static ?string $modelLabel = 'Header Contact';

    protected static ?string $pluralModelLabel = 'Header Contacts';

    protected static string | \UnitEnum | null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return HeaderContactForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HeaderContactTable::configure($table);
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
            'index' => Pages\ListHeaderContacts::route('/'),
            'create' => Pages\CreateHeaderContact::route('/create'),
            'view' => Pages\ViewHeaderContact::route('/{record}'),
            'edit' => Pages\EditHeaderContact::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('position');
    }
}