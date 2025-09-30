<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;

class TableBlock
{
    public static function make(): Block
    {
        return Block::make('table')
            ->icon('heroicon-o-table-cells')
            ->schema([
                TextInput::make('title')
                    ->label('Table Title (optional)'),
                Repeater::make('columns')
                    ->label('Columns')
                    ->schema([
                        TextInput::make('header')
                            ->required()
                            ->label('Column Header'),
                    ])
                    ->columns(3)
                    ->required()
                    ->minItems(1),
                Repeater::make('rows')
                    ->label('Rows')
                    ->schema([
                        Repeater::make('cells')
                            ->label('Cells')
                            ->schema([
                                RichEditor::make('content')
                                    ->label('Cell Content')
                                    ->required()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'bulletList',
                                        'orderedList',
                                    ]),
                            ])
                            ->columns(3)
                            ->minItems(1),
                    ])
                    ->defaultItems(1)
                    ->required()
                    ->minItems(1),
            ]);
    }
}