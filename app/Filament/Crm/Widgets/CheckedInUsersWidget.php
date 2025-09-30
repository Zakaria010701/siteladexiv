<?php

namespace App\Filament\Crm\Widgets;

use Filament\Tables\Columns\TextColumn;
use App\Models\TimeReport;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CheckedInUsersWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                TimeReport::query()
                    ->where('date', today())
                    ->whereNotNull('time_in')
            )
            ->columns([
                TextColumn::make('user.name')
                    ->sortable(),
                TextColumn::make('time_in')
                    ->dateTime(getTimeFormat())
                    ->sortable(),
                TextColumn::make('time_in_status')
                    ->badge(),
            ]);
    }
}
