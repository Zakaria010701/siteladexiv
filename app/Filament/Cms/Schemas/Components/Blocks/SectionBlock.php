<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\TextInput;

class SectionBlock
{
    public static function make(): Block
    {
        return Block::make('section')
            ->icon('heroicon-o-squares-plus')
            ->label('Section Container')
            ->schema([
                TextInput::make('title')
                    ->label('Section Title (optional)')
                    ->placeholder('Leave empty for no title'),

                Select::make('background_type')
                    ->label('Background Type')
                    ->options([
                        'transparent' => 'Transparent',
                        'solid' => 'Solid Color',
                        'gradient' => 'Gradient',
                        'glass' => 'Glassmorphism',
                    ])
                    ->default('transparent')
                    ->required()
                    ->live(),

                ColorPicker::make('background_color')
                    ->label('Background Color')
                    ->default('#ffffff')
                    ->visible(fn ($get) => in_array($get('background_type'), ['solid', 'glass'])),

                Select::make('gradient_type')
                    ->label('Gradient Style')
                    ->options([
                        'primary' => 'Primary (Blue Gradient)',
                        'medical' => 'Medical (Blue to White)',
                        'warm' => 'Warm (Orange to Pink)',
                        'cool' => 'Cool (Teal to Blue)',
                        'rainbow' => 'Rainbow (Full Spectrum)',
                    ])
                    ->default('primary')
                    ->visible(fn ($get) => $get('background_type') === 'gradient'),

                Slider::make('padding_top')
                   ->label('Padding Top (rem)')
                   ->minValue(0)
                   ->maxValue(20)
                   ->step(1)
                   ->default(8),

               Slider::make('padding_bottom')
                   ->label('Padding Bottom (rem)')
                   ->minValue(0)
                   ->maxValue(20)
                   ->step(1)
                   ->default(8),

               Slider::make('margin_top')
                   ->label('Margin Top (rem)')
                   ->minValue(0)
                   ->maxValue(20)
                   ->step(1)
                   ->default(0),

               Slider::make('margin_bottom')
                   ->label('Margin Bottom (rem)')
                   ->minValue(0)
                   ->maxValue(20)
                   ->step(1)
                   ->default(0),

                Select::make('border_style')
                    ->label('Border Style')
                    ->options([
                        'none' => 'No Border',
                        'solid' => 'Solid Border',
                        'dashed' => 'Dashed Border',
                        'gradient' => 'Gradient Border',
                    ])
                    ->default('none'),

                ColorPicker::make('border_color')
                    ->label('Border Color')
                    ->default('#3991B3')
                    ->visible(fn ($get) => $get('border_style') !== 'none'),

                Select::make('corner_radius')
                    ->label('Corner Radius')
                    ->options([
                        'none' => 'Sharp Corners',
                        'sm' => 'Small (0.25rem)',
                        'md' => 'Medium (0.5rem)',
                        'lg' => 'Large (0.75rem)',
                        'xl' => 'Extra Large (1rem)',
                        'full' => 'Fully Rounded',
                    ])
                    ->default('lg'),

                Select::make('shadow_style')
                    ->label('Shadow Style')
                    ->options([
                        'none' => 'No Shadow',
                        'sm' => 'Small Shadow',
                        'md' => 'Medium Shadow',
                        'lg' => 'Large Shadow',
                        'xl' => 'Extra Large Shadow',
                    ])
                    ->default('md'),
            ]);
    }
}