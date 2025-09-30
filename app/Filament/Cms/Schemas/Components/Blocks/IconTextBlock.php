<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\IconPicker;
use Filament\Support\Icons\Heroicon;

class IconTextBlock
{
    public static function make(): Block
    {
        return Block::make('icon-text')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->schema([
                TextInput::make('title')
                    ->label('Block Title (optional)'),
                \Filament\Forms\Components\Repeater::make('items')
                    ->label('Icon Items')
                    ->schema([
                        Select::make('icon')
                            ->label('Icon')
                            ->options([
                                'fa fa-phone' => 'Phone',
                                'fa fa-map-marker-alt' => 'Location',
                                'fa fa-info-circle' => 'Info',
                                'fa fa-clock' => 'Clock',
                                'fa fa-envelope' => 'Email',
                                'fa fa-user' => 'User',
                                'fa fa-calendar' => 'Calendar',
                                'fa fa-check' => 'Check',
                                'fa fa-home' => 'Home',
                                'fa fa-globe' => 'Globe',
                            ])
                            ->default('fa fa-info')
                            ->required(),
                        Select::make('layout')
                            ->label('Layout')
                            ->options([
                                'left-right' => 'Icon left, Text right',
                                'above-below' => 'Icon above, Text below',
                                'overlay' => 'Text over Icon',
                            ])
                            ->default('above-below')
                            ->required(),
                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'text' => 'Text',
                                'phone' => 'Phone Number',
                            ])
                            ->default('text')
                            ->required(),
                        TextInput::make('value')
                            ->label('Text or Phone Number')
                            ->required()
                            ->tel(fn ($get) => $get('type') === 'phone'),
                    ])
                    ->columns(3)
                    ->defaultItems(1)
                    ->minItems(1),
            ]);
    }
}