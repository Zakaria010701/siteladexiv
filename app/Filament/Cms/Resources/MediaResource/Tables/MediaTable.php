<?php

namespace App\Filament\Cms\Resources\MediaResource\Tables;

use App\Models\MediaItem;
use Exception;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class MediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_url')
                    ->label('Preview')
                    ->circular()
                    ->size(60)
                    ->defaultImageUrl('/images/placeholder.png')
                    ->getStateUsing(function ($record) {
                        // Get the first media file and return its URL
                        $mediaFile = $record->mediaFiles->first();
                        if ($mediaFile) {
                            try {
                                // Try to get thumb conversion URL, fallback to original
                                return $mediaFile->getUrl('thumb');
                            } catch (Exception $e) {
                                // Fallback to original URL if conversion doesn't exist
                                return $mediaFile->getUrl();
                            }
                        }
                        return null;
                    }),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'image' => 'success',
                        'icon' => 'warning',
                        'document' => 'info',
                    }),

                TextColumn::make('collection')
                    ->badge()
                    ->color('gray'),

                IconColumn::make('is_public')
                    ->label('Public')
                    ->boolean(),

                TextColumn::make('tags')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'image' => 'Image',
                        'icon' => 'Icon',
                        'document' => 'Document',
                    ]),

                SelectFilter::make('collection')
                    ->options(fn (): array => MediaItem::query()
                        ->select('collection')
                        ->distinct()
                        ->pluck('collection', 'collection')
                        ->toArray()
                    ),

                TernaryFilter::make('is_public')
                    ->label('Public Status'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}