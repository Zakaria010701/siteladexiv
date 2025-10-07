<?php

namespace App\Filament\Cms\Resources\CmsPages\Schemas\Components;

use Filament\Forms\Components\RichEditor;

class CmsPageContentEditor
{
    public static function make(): RichEditor
    {
        return RichEditor::make('content')
            ->columnSpanFull()
            ->json()
            ->toolbarButtons([
                'attachFiles',
                'bold',
                'bulletList',
                'codeBlock',
                'h2',
                'h3',
                'italic',
                'link',
                'orderedList',
                'quote',
                'redo',
                'strike',
                'underline',
                'undo',
            ]);
    }
}