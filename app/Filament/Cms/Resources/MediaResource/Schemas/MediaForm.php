<?php

 namespace App\Filament\Cms\Resources\MediaResource\Schemas;

 use Exception;
 use Filament\Forms\Components\TextInput;
 use Filament\Forms\Components\Textarea;
 use Filament\Forms\Components\Select;
 use Filament\Forms\Components\TagsInput;
 use Filament\Forms\Components\Toggle;
 use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
 use Filament\Schemas\Components\Section;
 use Filament\Schemas\Schema;
 use Filament\Infolists\Components\ImageEntry;
 use Filament\Infolists\Components\TextEntry;
 use Filament\Infolists\Components\RepeatableEntry;

 class MediaForm
 {
     public static function configure(Schema $schema): Schema
     {
         return $schema
             ->columns([
                 'lg' => 2,
             ])
             ->components([
                 Section::make('Media Information')
                     ->columns(2)
                     ->columnSpan(1)
                     ->schema([
                         TextInput::make('name')
                             ->required()
                             ->maxLength(255)
                             ->live(onBlur: true)
                             ->afterStateUpdated(fn (string $state, callable $set) => $set('collection', str()->slug($state))),

                         TextInput::make('alt')
                             ->label('Alt Text')
                             ->maxLength(255),

                         Textarea::make('description')
                             ->maxLength(65535)
                             ->columnSpanFull(),

                         Select::make('type')
                             ->options([
                                 'image' => 'Image',
                                 'icon' => 'Icon',
                                 'document' => 'Document',
                             ])
                             ->default('image')
                             ->required(),

                         TextInput::make('collection')
                             ->default('default')
                             ->maxLength(255),

                         TagsInput::make('tags')
                             ->placeholder('Add tags...'),

                         Toggle::make('is_public')
                             ->label('Public')
                             ->default(true),
                     ]),

                 Section::make('Media Files')
                     ->columnSpan(1)
                     ->schema([
                         SpatieMediaLibraryFileUpload::make('mediaFiles')
                             ->label('Upload Media Files')
                             ->multiple()
                             ->collection('default')
                             ->disk('public')
                             ->imagePreviewHeight('250')
                             ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                             ->maxSize(5120)
                             ->columnSpanFull(),
                     ]),
             ]);
     }

     public static function viewSchema(Schema $schema): Schema
     {
         return $schema
             ->columns([
                 'lg' => 2,
             ])
             ->components([
                 Section::make('Media Information')
                     ->columns(2)
                     ->columnSpan(1)
                     ->schema([
                         TextEntry::make('name')
                             ->label('Name'),

                         TextEntry::make('type')
                             ->label('Type')
                             ->badge()
                             ->color(fn (string $state): string => match ($state) {
                                 'image' => 'success',
                                 'icon' => 'warning',
                                 'document' => 'info',
                             }),

                         TextEntry::make('collection')
                             ->label('Collection')
                             ->badge()
                             ->color('gray'),

                         TextEntry::make('is_public')
                             ->label('Public')
                             ->boolean(),

                         TextEntry::make('tags')
                             ->label('Tags')
                             ->badge()
                             ->color('gray'),

                         TextEntry::make('created_at')
                             ->label('Created At')
                             ->dateTime(),

                         TextEntry::make('updated_at')
                             ->label('Updated At')
                             ->dateTime(),
                     ]),

                 Section::make('Media Files')
                     ->columnSpan(1)
                     ->schema([
                         ImageEntry::make('preview_url')
                             ->label('Preview')
                             ->height(300)
                             ->width('100%')
                             ->defaultImageUrl('/images/placeholder.png')
                             ->getStateUsing(function ($record) {
                                 $mediaFile = $record->mediaFiles->first();
                                 if ($mediaFile) {
                                     try {
                                         // Try to get preview conversion URL, fallback to original
                                         return $mediaFile->getUrl('preview');
                                     } catch (Exception $e) {
                                         try {
                                             // Fallback to original URL if conversion doesn't exist
                                             return $mediaFile->getUrl();
                                         } catch (Exception $e2) {
                                             return null;
                                         }
                                     }
                                 }
                                 return null;
                             })
                             ->visible(function ($record) {
                                 $mediaFile = $record->mediaFiles->first();
                                 return $mediaFile && str_starts_with($mediaFile->mime_type ?? '', 'image/');
                             }),

                         TextEntry::make('url')
                             ->label('Original Image')
                             ->getStateUsing(function ($record) {
                                 $mediaFile = $record->mediaFiles->first();
                                 return $mediaFile ? $mediaFile->getUrl() : null;
                             }),
                     ]),
             ]);
     }
 }