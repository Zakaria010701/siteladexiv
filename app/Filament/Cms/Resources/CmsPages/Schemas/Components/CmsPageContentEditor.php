<?php

namespace App\Filament\Cms\Resources\CmsPages\Schemas\Components;

use App\Filament\Cms\Schemas\Components\Blocks\CmsTextEditorBlock;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\RichEditor;

class CmsPageContentEditor
{
    public static function make(): RichEditor
    {
        return RichEditor::make('content')
            ->columnSpanFull()
            ->json();
    }
}