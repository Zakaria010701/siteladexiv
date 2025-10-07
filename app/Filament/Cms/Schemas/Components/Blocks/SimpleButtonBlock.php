<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use App\Models\CmsPage;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Support\Icons\Heroicon;

class SimpleButtonBlock
{
    public static function make(): Block
    {
        return Block::make('simple_button')
            ->label('Button')
            ->icon('heroicon-o-cursor-arrow-rays')
            ->schema([
                Repeater::make('buttons')
                    ->label('Buttons')
                    ->schema([
                        TextInput::make('button_text')
                            ->label('Button Text')
                            ->required()
                            ->default('Click here'),

                        TextInput::make('button_title')
                            ->label('Button Title (Optional)')
                            ->placeholder('Optional title above the button'),

                        Textarea::make('button_description')
                            ->label('Button Description (Optional)')
                            ->placeholder('Optional description below the button')
                            ->rows(2)
                            ->maxLength(255),

                        Select::make('target_page_id')
                            ->label('Target Page (Optional)')
                            ->options(function () {
                                return CmsPage::published()
                                    ->pluck('title', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a CMS page (optional)'),

                        TextInput::make('custom_url')
                            ->label('Custom URL/Link (Optional)')
                            ->placeholder('https://example.com/download.pdf')
                            ->url()
                            ->suffixIcon('heroicon-o-link')
                            ->helperText('For downloads or external links. If both page and custom URL are set, custom URL takes priority.'),

                        Select::make('button_style')
                            ->label('Button Style')
                            ->options([
                                'primary' => 'Primary (Blue)',
                                'secondary' => 'Secondary (Gray)',
                                'outline' => 'Outline (Blue border)',
                            ])
                            ->default('primary')
                            ->required(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['button_text'] ?? 'New Button')
                    ->defaultItems(1)
                    ->addActionLabel('Add Button')
                    ->minItems(1)
                    ->maxItems(6),
            ]);
    }
}