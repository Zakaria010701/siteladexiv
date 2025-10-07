<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

class FaqBlock
{
    public static function make(): Block
    {
        return Block::make('faq')
            ->label('FAQ')
            ->icon('heroicon-o-question-mark-circle')
            ->schema([
                TextInput::make('title')
                    ->label('FAQ Title (Optional)')
                    ->placeholder('Frequently Asked Questions'),

                Repeater::make('faqs')
                    ->label('FAQ Items')
                    ->schema([
                        TextInput::make('question')
                            ->label('Question')
                            ->required()
                            ->placeholder('Enter your question'),

                        Textarea::make('answer')
                            ->label('Answer')
                            ->required()
                            ->placeholder('Enter the answer')
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['question'] ?? 'New FAQ')
                    ->defaultItems(1)
                    ->addActionLabel('Add FAQ')
                    ->minItems(1)
                    ->maxItems(20),
            ]);
    }
}