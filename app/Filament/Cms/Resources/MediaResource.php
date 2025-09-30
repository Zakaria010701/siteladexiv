<?php

 namespace App\Filament\Cms\Resources;

 use App\Filament\Cms\Resources\MediaResource\Pages;
 use App\Filament\Cms\Resources\MediaResource\Schemas\MediaForm;
 use App\Filament\Cms\Resources\MediaResource\Tables\MediaTable;
 use App\Models\MediaItem;
 use BackedEnum;
 use Filament\Forms;
 use Filament\Forms\Components;
 use Filament\Resources\Resource;
 use Filament\Schemas\Schema;
 use Filament\Support\Icons\Heroicon;
 use Filament\Tables\Table;
 use Illuminate\Database\Eloquent\Builder;

 class MediaResource extends Resource
 {
     protected static ?string $model = MediaItem::class;

     protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

     protected static ?string $navigationLabel = 'Media Gallery';

     protected static ?string $modelLabel = 'Media Item';

     protected static ?string $pluralModelLabel = 'Media Gallery';

     protected static ?int $navigationSort = 3;

     public static function form(Schema $schema): Schema
     {
         return MediaForm::configure($schema);
     }

    public static function table(Table $table): Table
    {
        return MediaTable::configure($table);
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
            'index' => Pages\ListMedia::route('/'),
            'create' => Pages\CreateMedia::route('/create'),
            'view' => Pages\ViewMedia::route('/{record}'),
            'edit' => Pages\EditMedia::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['mediaFiles']);
    }
}