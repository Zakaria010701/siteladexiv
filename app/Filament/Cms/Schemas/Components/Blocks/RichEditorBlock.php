<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use App\Models\CmsPage;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

class RichEditorBlock
{
    public static function make(): Block
    {
        return Block::make('editor')
            ->icon(Heroicon::DocumentText)
            ->schema([
                RichEditor::make('content')
                    ->label('Content')
                    ->required()
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'h2',
                        'h3',
                        'bulletList',
                        'orderedList',
                        'blockquote',
                        'codeBlock',
                        'link',
                    ])
                    ->hint('💡 Tipp: Du kannst Buttons später über separate Button-Blöcke hinzufügen.')
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
                        'blockquote',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ])
                    ->columnSpanFull(),

            ]);
    }
}
