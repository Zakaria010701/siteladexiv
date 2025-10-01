<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class TabsBlock
{
    public static function make(): Block
    {
        return Block::make('tabs')
            ->icon('heroicon-o-squares-2x2')
            ->label('Tabs')
            ->schema([
                TextInput::make('title')
                    ->label('Block Title (optional)')
                    ->placeholder('Leave empty to hide title'),

                Select::make('tab_style')
                    ->label('Tab Style')
                    ->options([
                        'underline' => 'Underline',
                        'pills' => 'Pills',
                        'buttons' => 'Buttons',
                    ])
                    ->default('underline')
                    ->required(),

                Select::make('tab_position')
                    ->label('Tab Position')
                    ->options([
                        'top' => 'Top',
                        'bottom' => 'Bottom',
                        'left' => 'Left',
                        'right' => 'Right',
                    ])
                    ->default('top')
                    ->required(),

                ColorPicker::make('active_tab_color')
                    ->label('Active Tab Color')
                    ->default('#3991B3'),

                ColorPicker::make('inactive_tab_color')
                    ->label('Inactive Tab Color')
                    ->default('#6b7280'),

                ColorPicker::make('content_background_color')
                    ->label('Content Background Color')
                    ->default('#ffffff'),

                Slider::make('padding_top')
                    ->label('Padding Top (rem)')
                    ->minValue(0)
                    ->maxValue(20)
                    ->step(1)
                    ->default(4),

                Slider::make('padding_bottom')
                    ->label('Padding Bottom (rem)')
                    ->minValue(0)
                    ->maxValue(20)
                    ->step(1)
                    ->default(4),

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
                    ])
                    ->default('none'),

                ColorPicker::make('border_color')
                    ->label('Border Color')
                    ->default('#e5e7eb')
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
                    ->default('md'),

                Select::make('shadow_style')
                    ->label('Shadow Style')
                    ->options([
                        'none' => 'No Shadow',
                        'sm' => 'Small Shadow',
                        'md' => 'Medium Shadow',
                        'lg' => 'Large Shadow',
                        'xl' => 'Extra Large Shadow',
                    ])
                    ->default('sm'),

                Repeater::make('tabs')
                    ->label('Tabs')
                    ->schema([
                        TextInput::make('tab_title')
                            ->label('Tab Title')
                            ->required()
                            ->maxLength(50),

                        Textarea::make('tab_content')
                            ->label('Tab Content')
                            ->required()
                            ->rows(6)
                            ->maxLength(2000)
                            ->placeholder('Content for this tab...'),

                        ColorPicker::make('custom_color')
                            ->label('Custom Tab Color (optional)')
                            ->placeholder('Leave empty to use default colors'),
                    ])
                    ->columns(1)
                    ->defaultItems(2)
                    ->minItems(1)
                    ->maxItems(8)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}