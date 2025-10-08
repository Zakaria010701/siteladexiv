<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class SimpleImageBlock
{
    public static function make(): Block
    {
        return Block::make('simple_image')
            ->icon('heroicon-o-photo')
            ->label('Einfaches Bild')
            ->schema([
                FileUpload::make('images')
                    ->disk('public')
                    ->directory('cms-pages')
                    ->image()
                    ->imagePreviewHeight('150')
                    ->maxSize(5120)
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp'])
                    ->openable()
                    ->downloadable()
                    ->deletable()
                    ->preserveFilenames()
                    ->multiple()
                    ->maxFiles(4)
                    ->minFiles(1)
                    ->label('Bilder hochladen (bis zu 4)')
                    ->required(),

                TextInput::make('alt_text')
                    ->label('Alt-Text')
                    ->placeholder('Beschreibung für Barrierefreiheit')
                    ->maxLength(200),

                Select::make('alignment')
                    ->label('Ausrichtung')
                    ->options([
                        'left' => 'Links',
                        'center' => 'Zentriert',
                        'right' => 'Rechts',
                    ])
                    ->default('center')
                    ->required(),

                Select::make('size')
                    ->label('Größe')
                    ->options([
                        'small' => 'Klein (25%)',
                        'medium' => 'Mittel (50%)',
                        'large' => 'Groß (75%)',
                        'full' => 'Vollbreite (100%)',
                    ])
                    ->default('full')
                    ->required(),
            ]);
    }
}