<?php

namespace App\Filament\Crm\Resources\Customers\Schemas\Components;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class CustomerMediaSection
{
    public static function make(): Section
    {
        return Section::make(__('Media'))
            ->columnSpan(1)
            ->compact()
            ->collapsed(true)
            ->schema([
                SpatieMediaLibraryFileUpload::make('media')
                    ->disk('public')
                    ->hiddenOn('create')
                    ->downloadable(true)
                    ->multiple(),
            ]);
    }
}
