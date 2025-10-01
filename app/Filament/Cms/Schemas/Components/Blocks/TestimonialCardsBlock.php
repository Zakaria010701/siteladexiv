<?php

namespace App\Filament\Cms\Schemas\Components\Blocks;

use App\Filament\Cms\Schemas\Components\MediaSelector;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class TestimonialCardsBlock
{
    public static function make(): Block
    {
        return Block::make('testimonial-cards')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->label('Testimonial Cards')
            ->schema([
                TextInput::make('title')
                   ->label('Block Title (optional)')
                   ->placeholder('Leave empty to use default content'),

               Select::make('background_type')
                   ->label('Background Type')
                   ->options([
                       'transparent' => 'Transparent',
                       'solid' => 'Solid Color',
                       'gradient' => 'Gradient',
                       'glass' => 'Glassmorphism',
                   ])
                   ->default('transparent')
                   ->required()
                   ->live(),

               ColorPicker::make('background_color')
                   ->label('Background Color')
                   ->default('#ffffff')
                   ->visible(fn ($get) => in_array($get('background_type'), ['solid', 'glass', 'transparent'])),

               Select::make('gradient_type')
                   ->label('Gradient Style')
                   ->options([
                       'primary' => 'Primary (Blue Gradient)',
                       'medical' => 'Medical (Blue to White)',
                       'warm' => 'Warm (Orange to Pink)',
                       'cool' => 'Cool (Teal to Blue)',
                       'rainbow' => 'Rainbow (Full Spectrum)',
                   ])
                   ->default('primary')
                   ->visible(fn ($get) => $get('background_type') === 'gradient'),

               Slider::make('padding_top')
                   ->label('Padding Top (rem)')
                   ->minValue(0)
                   ->maxValue(20)
                   ->step(1)
                   ->default(8),

               Slider::make('padding_bottom')
                   ->label('Padding Bottom (rem)')
                   ->minValue(0)
                   ->maxValue(20)
                   ->step(1)
                   ->default(8),

               Slider::make('margin_top')
                   ->label('Margin Top (rem)')
                   ->minValue(0)
                   ->maxValue(20)
                   ->step(1)
                   ->default(0),

               Slider::make('margin_bottom')
                   ->label('Margin Bottom (rem)')
                   ->minValue(0)
                   ->maxValue(20)
                   ->step(1)
                   ->default(0),

               Select::make('border_style')
                   ->label('Border Style')
                   ->options([
                       'none' => 'No Border',
                       'solid' => 'Solid Border',
                       'dashed' => 'Dashed Border',
                       'gradient' => 'Gradient Border',
                   ])
                   ->default('none'),

               ColorPicker::make('border_color')
                   ->label('Border Color')
                   ->default('#3991B3')
                   ->visible(fn ($get) => $get('border_style') !== 'none'),

               Select::make('corner_radius')
                   ->label('Corner Radius')
                   ->options([
                       'none' => 'Sharp Corners',
                       'sm' => 'Small (0.25rem)',
                       'md' => 'Medium (0.5rem)',
                       'lg' => 'Large (0.75rem)',
                       'xl' => 'Extra Large (1rem)',
                       'full' => 'Fully Rounded',
                   ])
                   ->default('lg'),

               Select::make('shadow_style')
                   ->label('Shadow Style')
                   ->options([
                       'none' => 'No Shadow',
                       'sm' => 'Small Shadow',
                       'md' => 'Medium Shadow',
                       'lg' => 'Large Shadow',
                       'xl' => 'Extra Large Shadow',
                   ])
                   ->default('md'),

               Repeater::make('testimonials')
                    ->label('Testimonials')
                    ->schema([
                        Select::make('image_type')
                            ->label('Image Type')
                            ->options([
                                'upload' => 'Upload New Image',
                                'media' => 'Select from Media Library',
                                'none' => 'No Image',
                            ])
                            ->default('none')
                            ->required()
                            ->live(),

                        FileUpload::make('image')
                            ->label('Upload Profile Image')
                            ->image()
                            ->directory('cms/testimonials')
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
                            ->label('No profile image will be displayed')
                            ->visible(fn ($get) => $get('image_type') === 'none')
                            ->content('The testimonial will be displayed without a profile image.'),

                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('position')
                            ->label('Position/Company')
                            ->maxLength(100),

                        Textarea::make('testimonial')
                            ->label('Testimonial Text')
                            ->required()
                            ->rows(4)
                            ->maxLength(500)
                            ->placeholder('What did the customer say about your services?'),

                        Select::make('rating')
                            ->label('Rating')
                            ->options([
                                '5' => '5 Stars - Excellent',
                                '4' => '4 Stars - Very Good',
                                '3' => '3 Stars - Good',
                                '2' => '2 Stars - Fair',
                                '1' => '1 Star - Poor',
                            ])
                            ->default('5')
                            ->required(),
                    ])
                    ->columns(2)
                    ->defaultItems(4)
                    ->minItems(1)
                    ->maxItems(8)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}