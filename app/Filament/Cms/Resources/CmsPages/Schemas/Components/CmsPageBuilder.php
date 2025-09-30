<?php

namespace App\Filament\Cms\Resources\CmsPages\Schemas\Components;

use App\Filament\Cms\Schemas\Components\Blocks\ImageTextBlock;
use App\Filament\Cms\Schemas\Components\Blocks\RichEditorBlock;
use App\Filament\Cms\Schemas\Components\Blocks\ShopBlock;
use App\Filament\Cms\Schemas\Components\Blocks\SliderBlock;
use App\Filament\Cms\Schemas\Components\Blocks\TableBlock;
use App\Filament\Cms\Schemas\Components\Blocks\TitleBlock;
use Filament\Forms\Components\Builder;

class CmsPageBuilder
{
    public static function make(): Builder
    {
        return Builder::make('content')
            ->blocks([
                TitleBlock::make(),
                RichEditorBlock::make(),
                ImageTextBlock::make(),
                \App\Filament\Cms\Schemas\Components\Blocks\IconTextBlock::make(),
                \App\Filament\Cms\Schemas\Components\Blocks\BoxBlock::make(),
                SliderBlock::make(),
                TableBlock::make(),
                ShopBlock::make(),
            ]);
    }
}
