<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\RichEditor;
use Filament\Support\Icons\Heroicon;

class RichEditorBlock
{
    public static function make(): Block
    {
        return Block::make('editor')
            ->icon(Heroicon::DocumentText)
            ->schema([
                RichEditor::make('content')
                    ->required(),
            ]);
    }
}
