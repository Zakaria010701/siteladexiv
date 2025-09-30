<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use App\Filament\Cms\Schemas\Components\MediaSelector;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\ColorPicker;

class SliderBlock
{
    public static function make(): Block
    {
        return Block::make('slider')
            ->icon('heroicon-o-photo')
            ->schema([
                TextInput::make('title')
                    ->label('Slider Title (optional)'),
                \Filament\Forms\Components\ColorPicker::make('title_color')
                    ->label('Title Color')
                    ->default('#000000')
                    ->visible(fn ($get) => !empty($get('title'))),
                FileUpload::make('images')
                    ->label('Upload New Images')
                    ->disk('public')
                    ->directory('slider-images')
                    ->visibility('public')
                    ->image()
                    ->multiple()
                    ->maxFiles(10),
                MediaSelector::make('media_ids')
                    ->label('Or Select from Media Gallery')
                    ->multiple(),
                Toggle::make('autoplay')
                    ->label('Autoplay')
                    ->default(false),
                TextInput::make('autoplay_delay')
                    ->label('Autoplay Delay (ms)')
                    ->numeric()
                    ->default(3000)
                    ->minValue(1000)
                    ->maxValue(10000)
                    ->visible(fn ($get) => $get('autoplay')),
            ]);
    }
}