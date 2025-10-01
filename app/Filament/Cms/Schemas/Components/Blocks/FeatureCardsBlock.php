<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use App\Filament\Cms\Schemas\Components\MediaSelector;
use App\Models\CmsPage;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class FeatureCardsBlock
{
    public static function make(): Block
    {
        return Block::make('feature-cards')
            ->icon('heroicon-o-squares-2x2')
            ->label('Feature Cards')
            ->schema([
                TextInput::make('title')
                    ->label('Block Title (optional)')
                    ->placeholder('Leave empty to use default content'),

                Select::make('card_type')
                    ->label('Card Type')
                    ->options([
                        'services' => 'Services Cards',
                        'testimonials' => 'Testimonial Cards',
                        'features' => 'Feature Cards',
                    ])
                    ->default('services')
                    ->required(),

                Repeater::make('cards')
                    ->label('Cards')
                    ->schema([
                        Select::make('image_type')
                            ->label('Image Type')
                            ->options([
                                'upload' => 'Upload New Image',
                                'media' => 'Select from Media Library',
                                'none' => 'No Image (Use Icon)',
                            ])
                            ->default('none')
                            ->required()
                            ->live(),

                        FileUpload::make('image')
                            ->label('Upload Image')
                            ->image()
                            ->directory('cms/feature-cards')
                            ->maxSize(2048)
                            ->imagePreviewHeight('100')
                            ->loadingIndicatorPosition('left')
                            ->panelAspectRatio('square')
                            ->panelLayout('integrated')
                            ->removeUploadedFileButtonPosition('right')
                            ->uploadButtonPosition('left')
                            ->uploadProgressIndicatorPosition('left')
                            ->preserveFilenames()
                            ->acceptedFileTypes(['png', 'jpg', 'jpeg', 'webp', 'gif'])
                            ->visible(fn ($get) => $get('image_type') === 'upload'),

                        MediaSelector::make('media_id')
                            ->label('Select from Media Library')
                            ->visible(fn ($get) => $get('image_type') === 'media'),

                        Placeholder::make('no_image_info')
                            ->label('No image will be displayed - gradient icon will be used instead')
                            ->visible(fn ($get) => $get('image_type') === 'none')
                            ->content('The card will use a colored gradient icon instead of an image.'),

                        TextInput::make('title')
                            ->label('Card Title')
                            ->required()
                            ->maxLength(100),

                        Textarea::make('description')
                            ->label('Description')
                            ->required()
                            ->rows(3)
                            ->maxLength(300),

                        TextInput::make('name')
                            ->label('Name/Subtitle')
                            ->maxLength(100),

                        Select::make('rating')
                            ->label('Rating (for testimonials)')
                            ->options([
                                '1' => '1 Star',
                                '2' => '2 Stars',
                                '3' => '3 Stars',
                                '4' => '4 Stars',
                                '5' => '5 Stars',
                            ])
                            ->visible(fn ($get) => $get('../../card_type') === 'testimonials'),

                        Select::make('link_type')
                            ->label('Link Type')
                            ->options([
                                'url' => 'External URL',
                                'cms_page' => 'CMS Page',
                                'none' => 'No Link',
                            ])
                            ->default('none')
                            ->required()
                            ->live(),

                        TextInput::make('link_url')
                            ->label('External URL')
                            ->url()
                            ->placeholder('https://...')
                            ->visible(fn ($get) => $get('link_type') === 'url'),

                        Select::make('link_cms_page')
                            ->label('Select CMS Page')
                            ->options(function () {
                                return CmsPage::published()
                                    ->orderBy('title')
                                    ->pluck('title', 'id')
                                    ->map(function ($title, $id) {
                                        $page = CmsPage::find($id);
                                        return $title . ' (/' . $page->slug . ')';
                                    });
                            })
                            ->searchable()
                            ->placeholder('Choose a published page...')
                            ->visible(fn ($get) => $get('link_type') === 'cms_page'),
                    ])
                    ->columns(2)
                    ->defaultItems(4)
                    ->minItems(1)
                    ->maxItems(8)
                    ->collapsible(),
            ]);
    }
}