<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use App\Filament\Cms\Schemas\Components\MediaSelector;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
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
                    ->label('Titel (optional)')
                    ->placeholder('Titel für den Slider-Bereich'),

                RichEditor::make('content')
                    ->label('Textinhalt')
                    ->placeholder('Beschreibender Text neben dem Slider...')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'bulletList',
                        'orderedList',
                        'h2',
                        'h3',
                        'link',
                    ]),

                Select::make('slider_position')
                    ->label('Slider-Position')
                    ->options([
                        'left' => 'Links',
                        'right' => 'Rechts',
                    ])
                    ->default('left')
                    ->required(),

                FileUpload::make('images')
                    ->label('Bilder hochladen')
                    ->disk('public')
                    ->directory('slider-images')
                    ->visibility('public')
                    ->image()
                    ->multiple()
                    ->maxFiles(10)
                    ->imagePreviewHeight('150')
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp'])
                    ->openable()
                    ->downloadable()
                    ->deletable()
                    ->preserveFilenames(),

                MediaSelector::make('media_ids')
                    ->label('Oder aus Medien-Galerie auswählen')
                    ->multiple(),

                Toggle::make('autoplay')
                    ->label('Autoplay')
                    ->default(false),

                TextInput::make('autoplay_delay')
                    ->label('Autoplay-Verzögerung (ms)')
                    ->numeric()
                    ->default(3000)
                    ->minValue(1000)
                    ->maxValue(10000)
                    ->visible(fn ($get) => $get('autoplay')),

                \Filament\Forms\Components\ColorPicker::make('title_color')
                    ->label('Titel-Farbe')
                    ->default('#000000')
                    ->visible(fn ($get) => !empty($get('title'))),
            ]);
    }
}