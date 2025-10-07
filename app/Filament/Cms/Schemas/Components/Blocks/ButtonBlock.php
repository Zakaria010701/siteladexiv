<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use App\Models\CmsPage;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

class ButtonBlock
{
    public static function make(): Block
    {
        return Block::make('button')
            ->icon('heroicon-o-cursor-arrow-rays')
            ->schema([
                TextInput::make('button_text')
                    ->label('Button Text')
                    ->required()
                    ->default('Click here'),
                Select::make('target_page_id')
                    ->label('Target Page')
                    ->options(function () {
                        return CmsPage::published()
                            ->pluck('title', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->searchable(),
                Select::make('button_style')
                    ->label('Button Style')
                    ->options([
                        'primary' => 'Primary',
                        'secondary' => 'Secondary',
                        'outline' => 'Outline',
                    ])
                    ->default('primary')
                    ->required(),
                Select::make('button_size')
                    ->label('Button Size')
                    ->options([
                        'sm' => 'Small',
                        'md' => 'Medium',
                        'lg' => 'Large',
                    ])
                    ->default('md')
                    ->required(),
            ]);
    }
}