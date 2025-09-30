<?php

namespace App\Filament\Cms\Resources;

use App\Filament\Cms\Resources\AllMediaResource\Pages;
use App\Filament\Cms\Resources\AllMediaResource\Tables\AllMediaTable;
use App\Models\MediaItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class AllMediaResource extends Resource
{
    protected static ?string $model = MediaItem::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'All Media';

    protected static ?string $modelLabel = 'Media File';

    protected static ?string $pluralModelLabel = 'Media Gallery';

    protected static ?int $navigationSort = 4;

    protected static string|\UnitEnum|null $navigationGroup = 'Media Gallery';


    public static function table(Table $table): Table
    {
        return AllMediaTable::configure($table);
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
            'index' => Pages\ListAllMedia::route('/'),
            'view' => Pages\ViewAllMedia::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with('mediaFiles')
            ->whereHas('mediaFiles', function ($query) {
                $query->where('mime_type', 'like', 'image/%');
            })
            ->orderBy('created_at', 'desc');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereHas('mediaFiles', function ($query) {
            $query->where('mime_type', 'like', 'image/%');
        })->count();
    }
}