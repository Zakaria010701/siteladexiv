<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use App\Filament\Cms\Schemas\Components\MediaSelector;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class ImageTextBlock
{
    public static function make(): Block
    {
        return Block::make('image_text')
            ->icon('heroicon-o-photo')
            ->schema([
                TextInput::make('title')
                    ->label('Title (optional)'),
                RichEditor::make('content'),
                FileUpload::make('image')
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
                    ->label('Upload New Image'),
                MediaSelector::make('media_id')
                    ->label('Or Select from Media Gallery'),
                Select::make('image_position')
                    ->options([
                        'left' => 'Left',
                        'right' => 'Right',
                    ])
                    ->default('left')
                    ->required(),
            ]);
    }
}