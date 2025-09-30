<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use App\Models\Category;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;

class ShopBlock
{
    public static function make(): Block
    {
        return Block::make('shop')
            ->icon('heroicon-o-shopping-bag')
            ->schema([
                Select::make('gender')
                    ->label('Geschlecht')
                    ->options([
                        'female' => 'Frauen',
                        'male' => 'Herren',
                        'all' => 'Alle',
                    ])
                    ->default('all')
                    ->required(),
                Select::make('category_id')
                    ->label('Kategorie')
                    ->options(Category::where('name', '!=', null)->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }
}