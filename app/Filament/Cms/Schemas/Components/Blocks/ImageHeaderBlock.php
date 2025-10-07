<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use App\Filament\Cms\Schemas\Components\MediaSelector;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;

class ImageHeaderBlock
{
    public static function make(): Block
    {
        return Block::make('image_header')
            ->icon('heroicon-o-photo')
            ->label('Image Header')
            ->schema([
                TextInput::make('title')
                    ->label('Heading Text')
                    ->required()
                    ->placeholder('Enter heading text to display on the image'),
                FileUpload::make('image')
                    ->disk('public')
                    ->image()
                    ->label('Upload Background Image')
                    ->required(),
                MediaSelector::make('media_id')
                    ->label('Or Select Background Image from Media Gallery'),
                TextInput::make('height')
                    ->label('Height (optional)')
                    ->placeholder('e.g., 400px, 50vh')
                    ->helperText('Leave empty for default height (400px)')
                    ->default('400px'),
            ]);
    }
}