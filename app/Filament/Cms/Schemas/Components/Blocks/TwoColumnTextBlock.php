<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class TwoColumnTextBlock
{
    public static function make(): Block
    {
        return Block::make('two_column_text')
            ->icon('heroicon-o-queue-list')
            ->label('Zwei Spalten Text')
            ->schema([
                TextInput::make('heading')
                    ->label('Überschrift')
                    ->placeholder('Geben Sie hier Ihre Überschrift ein')
                    ->maxLength(200),

                Textarea::make('left_column')
                    ->label('Linke Spalte')
                    ->placeholder('Text für die linke Spalte...')
                    ->rows(6)
                    ->maxLength(1000)
                    ->columnSpanFull(),

                Textarea::make('right_column')
                    ->label('Rechte Spalte')
                    ->placeholder('Text für die rechte Spalte...')
                    ->rows(6)
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ]);
    }
}