<?php

namespace App\Filament\Tables\Columns;

use Filament\Tables\Columns\TextColumn;

class DeletedAtColumn
{
    public static function make(): TextColumn
    {
        return TextColumn::make('deleted_at')
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);
    }
}