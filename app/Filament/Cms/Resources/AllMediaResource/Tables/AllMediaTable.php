<?php

namespace App\Filament\Cms\Resources\AllMediaResource\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\MediaItem;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;

class AllMediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'lg' => 3,
                'xl' => 4,
            ])
            ->columns([
                Grid::make([
                    'default' => 1,
                ])
                ->schema([
                    Tables\Columns\ImageColumn::make('mediaFiles')
                        ->label('')
                        ->height(200)
                        ->width('100%')
                        ->getStateUsing(function (MediaItem $record) {
                            $mediaFile = $record->mediaFiles->first();
                            if ($mediaFile) {
                                try {
                                    // Get the original URL first to check if file exists
                                    $originalUrl = $mediaFile->getUrl();
                                    // Try to get thumb conversion URL
                                    $thumbUrl = $mediaFile->getUrl('thumb');
                                    \Log::info('Media file URLs', [
                                        'original' => $originalUrl,
                                        'thumb' => $thumbUrl,
                                        'media_id' => $mediaFile->id
                                    ]);
                                    return $thumbUrl;
                                } catch (\Exception $e) {
                                    \Log::error('Error getting media URL: ' . $e->getMessage());
                                    return '';
                                }
                            }
                            return '';
                        })
                        ->defaultImageUrl('/images/placeholder.png')
                        ->extraImgAttributes(['loading' => 'lazy']),

                    Stack::make([
                        Tables\Columns\TextColumn::make('name')
                            ->label('Media Item Name')
                            ->weight('bold')
                            ->size('sm'),

                        Tables\Columns\TextColumn::make('mediaFiles')
                            ->label('Files')
                            ->formatStateUsing(function ($state, MediaItem $record) {
                                return $record->mediaFiles->pluck('file_name')->join(', ');
                            })
                            ->size('xs')
                            ->color('gray'),

                        Tables\Columns\TextColumn::make('mediaFiles')
                            ->label('Size')
                            ->formatStateUsing(function ($state, MediaItem $record) {
                                $totalSize = $record->mediaFiles->sum('size');
                                return number_format($totalSize / 1024, 2) . ' KB';
                            })
                            ->size('xs')
                            ->color('gray'),

                        Tables\Columns\TextColumn::make('collection')
                            ->label('Collection')
                            ->size('xs')
                            ->color('gray'),

                        Tables\Columns\TextColumn::make('created_at')
                            ->label('Created')
                            ->dateTime('M j, Y')
                            ->size('xs')
                            ->color('gray'),
                    ])
                    ->space(1),
                ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('collection')
                    ->label('Collection')
                    ->options(fn (): array => MediaItem::query()
                        ->select('collection')
                        ->distinct()
                        ->pluck('collection', 'collection')
                        ->toArray()
                    )
                    ->placeholder('All collections'),

                Tables\Filters\Filter::make('images_only')
                    ->label('Images Only')
                    ->query(fn (Builder $query): Builder => $query->whereHas('mediaFiles', function ($q) {
                        $q->where('mime_type', 'like', 'image/%');
                    }))
                    ->default(true),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (MediaItem $record): string => $record->mediaFiles->first()?->getUrl() ?? '')
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No Media Items')
            ->emptyStateDescription('There are no media items created yet.')
            ->emptyStateIcon('heroicon-o-photo')
            ->searchable(['name', 'collection']);
    }
}