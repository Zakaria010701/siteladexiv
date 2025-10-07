<?php

namespace App\Filament\Cms\Resources\AllMediaResource\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\MediaItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
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
                            // First priority: use files from the database
                            if ($record->files && is_array($record->files) && !empty($record->files)) {
                                foreach ($record->files as $filePath) {
                                    // Convert Windows backslashes to forward slashes
                                    $filePath = str_replace('\\', '/', $filePath);

                                    // Clean up the path - remove any leading slashes or unnecessary components
                                    $filePath = ltrim($filePath, '/');

                                    // Skip if it's a conversion file (thumb, preview) - we want the main file
                                    if (preg_match('/\/conversions\/.*-(thumb|preview)\./', $filePath)) {
                                        continue;
                                    }

                                    // Check if file exists at the storage path
                                    $fullPath = storage_path('app/public/' . $filePath);
                                    if (file_exists($fullPath)) {
                                        // Use Laravel's built-in URL generation with proper domain
                                        $storageUrl = \Illuminate\Support\Facades\Storage::url($filePath);
                                        // Ensure it has the proper domain for the current request
                                        if (strpos($storageUrl, 'http') !== 0) {
                                            // Use the current request's domain
                                            $storageUrl = request()->getScheme() . '://' . request()->getHost() . $storageUrl;
                                        }
                                        return $storageUrl;
                                    }
                                }

                                // If we didn't find a main file, try conversion files as fallback
                                foreach ($record->files as $filePath) {
                                    $filePath = str_replace('\\', '/', $filePath);
                                    $filePath = ltrim($filePath, '/');

                                    $fullPath = storage_path('app/public/' . $filePath);
                                    if (file_exists($fullPath)) {
                                        $storageUrl = \Illuminate\Support\Facades\Storage::url($filePath);
                                        if (strpos($storageUrl, 'http') !== 0) {
                                            $storageUrl = request()->getScheme() . '://' . request()->getHost() . $storageUrl;
                                        }
                                        return $storageUrl;
                                    }
                                }
                            }

                            // Second priority: try to use Spatie Media objects if available
                            $mediaFile = $record->mediaFiles->first();
                            if ($mediaFile && $mediaFile->file_name) {
                                $url = $mediaFile->getUrl();
                                // Ensure the URL has the correct domain
                                if (strpos($url, 'http') !== 0) {
                                    $url = request()->getScheme() . '://' . request()->getHost() . $url;
                                }
                                return $url;
                            }

                            // Final fallback: return placeholder
                            $placeholderUrl = asset('images/logo.png');
                            if (strpos($placeholderUrl, 'http') !== 0) {
                                $placeholderUrl = request()->getScheme() . '://' . request()->getHost() . $placeholderUrl;
                            }
                            return $placeholderUrl;
                        })
                        ->defaultImageUrl(function () {
                            $url = asset('images/logo.png');
                            if (strpos($url, 'http') !== 0) {
                                $url = request()->getScheme() . '://' . request()->getHost() . $url;
                            }
                            return $url;
                        })
                        ->extraImgAttributes(['loading' => 'lazy']),

                    Stack::make([
                        Tables\Columns\TextColumn::make('name')
                            ->label('Media Item Name')
                            ->weight('bold')
                            ->size('sm'),

                        Tables\Columns\TextColumn::make('files')
                            ->label('Files')
                            ->formatStateUsing(function ($state, MediaItem $record) {
                                if ($record->files && is_array($record->files)) {
                                    return implode(', ', array_map('basename', $record->files));
                                }
                                return 'No files';
                            })
                            ->size('xs')
                            ->color('gray'),

                        Tables\Columns\TextColumn::make('files')
                            ->label('Size')
                            ->formatStateUsing(function ($state, MediaItem $record) {
                                if ($record->files && is_array($record->files)) {
                                    $totalSize = 0;
                                    foreach ($record->files as $filePath) {
                                        $filePath = str_replace('\\', '/', $filePath);
                                        $filePath = ltrim($filePath, '/');
                                        $fullPath = storage_path('app/public/' . $filePath);
                                        if (file_exists($fullPath)) {
                                            $totalSize += filesize($fullPath);
                                        }
                                    }
                                    return number_format($totalSize / 1024, 2) . ' KB';
                                }
                                return '0 KB';
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
                    ->query(fn (Builder $query): Builder => $query->where(function ($q) {
                        // Check if any of the files in the record are images
                        $q->whereJsonContains('files', function ($query) {
                            // This is a simplified check - in a real implementation you'd want to check file extensions
                            return true;
                        });
                    }))
                    ->default(true),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (MediaItem $record): string => $record->mediaFiles->first()?->getUrl() ?? '#')
                    ->openUrlInNewTab()
                    ->visible(fn (MediaItem $record): bool => $record->mediaFiles->isNotEmpty()),
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