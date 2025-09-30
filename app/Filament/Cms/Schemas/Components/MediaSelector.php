<?php

namespace App\Filament\Cms\Schemas\Components;

use App\Filament\Cms\Resources\AllMediaResource;
use App\Models\MediaItem;
use Filament\Forms\Components\Select;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaSelector extends Select
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Select from Media Gallery')
            ->placeholder('Choose an image from media gallery')
            ->options(function () {
                $resourceClass = AllMediaResource::class;
                $query = $resourceClass::getEloquentQuery();

                return $query->limit(100)->get()->mapWithKeys(function ($mediaItem) {
                    return [$mediaItem->id => $mediaItem->name . ' (' . $mediaItem->file_name . ')'];
                });
            })
            ->getOptionLabelFromRecordUsing(function ($value) {
                $mediaItem = MediaItem::find($value);
                if ($mediaItem) {
                    // Ensure we have a single model, not a collection
                    if (is_array($mediaItem) || $mediaItem instanceof \Illuminate\Database\Eloquent\Collection) {
                        $mediaItem = $mediaItem instanceof \Illuminate\Database\Eloquent\Collection ? $mediaItem->first() : $mediaItem;
                    }
                    return $mediaItem ? $mediaItem->name . ' (' . $mediaItem->file_name . ')' : 'Unknown Media';
                }
                return 'Unknown Media';
            })
            ->searchable(['name', 'file_name'])
            ->allowHtml()
            ->afterStateHydrated(function (MediaSelector $component, $state) {
                // If we have a media ID, get the URL for preview
                if ($state) {
                    $mediaItem = MediaItem::find($state);
                    if ($mediaItem) {
                        // Ensure we have a single model, not a collection
                        if (is_array($mediaItem) || $mediaItem instanceof \Illuminate\Database\Eloquent\Collection) {
                            $mediaItem = $mediaItem instanceof \Illuminate\Database\Eloquent\Collection ? $mediaItem->first() : $mediaItem;
                        }

                        if ($mediaItem && method_exists($mediaItem, 'mediaFiles') && $mediaItem->mediaFiles()->exists()) {
                            $spatieMedia = $mediaItem->mediaFiles()->first();
                            if ($spatieMedia) {
                                $component->hint($spatieMedia->getUrl());
                                $component->hintIcon('heroicon-o-photo');
                            }
                        }
                    }
                }
            });
    }
}