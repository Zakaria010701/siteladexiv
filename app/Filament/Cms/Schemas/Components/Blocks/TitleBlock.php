<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

class TitleBlock
{
    public static function make(): Block
    {
        return Block::make('title')
            ->icon('heroicon-o-h1')
            ->schema([
                TextInput::make('title')
                    ->required(),
                FileUpload::make('image')
                    ->disk('public')
                    ->image()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('16:9'),
                Select::make('position')
                    ->options([
                        'center' => 'Center',
                        'left' => 'Left',
                        'right' => 'Right',
                    ])
                    ->default('center')
                    ->required(),
            ]);
    }
}
