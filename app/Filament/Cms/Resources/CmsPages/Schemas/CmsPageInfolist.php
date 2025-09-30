<?php

namespace App\Filament\Cms\Resources\CmsPages\Schemas;

use App\Models\CmsPage;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Nette\Utils\Html;

class CmsPageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
                TextEntry::make('deleted_at')
                    ->dateTime(),
                TextEntry::make('status'),
                TextEntry::make('published_at')
                    ->dateTime(),
                TextEntry::make('title'),
                TextEntry::make('slug'),
            ]);
    }
}
