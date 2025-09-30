<?php

 namespace App\Filament\Cms\Resources\AllMediaResource\Pages;

 use App\Filament\Cms\Resources\AllMediaResource;
 use App\Models\MediaItem;
 use Filament\Actions;
 use Filament\Resources\Pages\ViewRecord;
 use Filament\Forms;
 use Illuminate\View\View;

 class ViewAllMedia extends ViewRecord
 {
     protected static string $resource = AllMediaResource::class;

     public function render(): \Illuminate\View\View
     {
         return view('filament.resources.all-media.view', [
             'record' => $this->getRecord(),
         ]);
     }

     protected function getHeaderActions(): array
     {
         return [
             Actions\Action::make('download')
                 ->label('Download')
                 ->icon('heroicon-o-arrow-down-tray')
                 ->url(function (MediaItem $record): string {
                     $mediaFile = $record->mediaFiles->first();
                     return $mediaFile?->getUrl() ?? '';
                 })
                 ->openUrlInNewTab()
                 ->visible(function (MediaItem $record): bool {
                     return $record->mediaFiles->isNotEmpty();
                 }),
         ];
     }

     public function getTitle(): string
     {
         return 'Media Item ansehen';
     }

     protected function getFormSchema(): array
     {
         return [];
     }
 }