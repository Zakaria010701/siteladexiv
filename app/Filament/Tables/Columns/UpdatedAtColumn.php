<?php

namespace App\Filament\Tables\Columns;

use Filament\Tables\Columns\TextColumn;

class UpdatedAtColumn
{
    public static function make(): TextColumn
    {
        return TextColumn::make('updated_at')
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);
    }
}