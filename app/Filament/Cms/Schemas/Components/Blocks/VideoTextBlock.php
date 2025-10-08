<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class VideoTextBlock
{
    public static function make(): Block
    {
        return Block::make('video_text')
            ->icon('heroicon-o-video-camera')
            ->label('Video mit Text')
            ->schema([
                TextInput::make('title')
                    ->label('Titel (optional)')
                    ->placeholder('Titel für den Video-Bereich'),

                RichEditor::make('content')
                    ->label('Textinhalt'),

                TextInput::make('video_url')
                    ->label('Video-URL')
                    ->placeholder('https://www.youtube.com/watch?v=... oder https://vimeo.com/...')
                    ->url()
                    ->required()
                    ->helperText('Unterstützt YouTube, Vimeo und andere Video-URLs'),

                Select::make('video_position')
                    ->label('Video-Position')
                    ->options([
                        'left' => 'Links',
                        'right' => 'Rechts',
                    ])
                    ->default('left')
                    ->required(),

                Select::make('aspect_ratio')
                    ->label('Seitenverhältnis')
                    ->options([
                        '16:9' => '16:9 (Standard)',
                        '4:3' => '4:3',
                        '1:1' => 'Quadratisch (1:1)',
                        '21:9' => '21:9 (Cinematic)',
                    ])
                    ->default('16:9')
                    ->required(),
            ]);
    }
}