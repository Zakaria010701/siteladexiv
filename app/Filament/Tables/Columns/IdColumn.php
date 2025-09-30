<?php

namespace App\Filament\Tables\Columns;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class IdColumn
{
    public static function make(): TextColumn
    {
        return TextColumn::make('id')
            ->sortable()
            ->visible(fn (Table $table) => auth()->user()->can('admin', $table->getModel()))
            ->toggleable(isToggledHiddenByDefault: true);
    }
}