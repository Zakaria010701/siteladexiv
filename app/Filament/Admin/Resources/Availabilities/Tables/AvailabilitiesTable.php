<?php

namespace App\Filament\Admin\Resources\Availabilities\Tables;

use App\Filament\Tables\Columns\CreatedAtColumn;
use App\Filament\Tables\Columns\DeletedAtColumn;
use App\Filament\Tables\Columns\IdColumn;
use App\Filament\Tables\Columns\UpdatedAtColumn;
use App\Models\Availability;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AvailabilitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                CreatedAtColumn::make(),
                UpdatedAtColumn::make(),
                DeletedAtColumn::make(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->date(getDateFormat())
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date(getDateFormat())
                    ->sortable(),
                TextColumn::make('planable.name')
                    ->url(fn (Availability $record) => $record->getPlanableUrl())
                    ->formatStateUsing(fn (Availability $record) => $record->getPlanableTitle()),
                TextColumn::make('availabilityType.name')
                    ->sortable()
                    ->badge()
                    ->color(fn (Availability $record) => Color::generateV3Palette($record->availabilityType->color)),
                ColorColumn::make('color')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_hidden')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_all_day')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_background')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_background_inverted')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton(),
                ActionGroup::make([
                    ActionGroup::make([
                        DeleteAction::make(),
                        RestoreAction::make(),
                        ForceDeleteAction::make(),
                    ])->dropdown(false),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
