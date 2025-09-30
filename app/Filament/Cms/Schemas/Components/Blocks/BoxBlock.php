<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;

class BoxBlock
{
    public static function make(): Block
    {
        return Block::make('box')
            ->icon('heroicon-o-squares-2x2')
            ->schema([
                Repeater::make('boxes')
                    ->label('Boxes')
                    ->schema([
                        Select::make('icon')
                            ->label('Icon')
                            ->options([
                                'fa fa-clock' => 'Clock',
                                'fa fa-shield-alt' => 'Shield',
                                'fa fa-phone' => 'Phone',
                                'fa fa-map-marker-alt' => 'Location',
                                'fa fa-info-circle' => 'Info',
                                'fa fa-check' => 'Check',
                                'fa fa-star' => 'Star',
                                'fa fa-heart' => 'Heart',
                                'fa fa-home' => 'Home',
                                'fa fa-globe' => 'Globe',
                            ])
                            ->default('fa fa-info-circle'),
                        \Filament\Forms\Components\ColorPicker::make('color')
                            ->label('Box Color')
                            ->default('#dbeafe'),
                        TextInput::make('title')
                            ->label('Title')
                            ->required(),
                        RichEditor::make('description')
                            ->label('Description')
                            ->required(),
                    ])
                    ->columns(1)
                    ->defaultItems(3)
                    ->minItems(1),
            ]);
    }
}