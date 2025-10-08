<?php

namespace App\Enums\Cms;

use Filament\Forms\Components\RichEditor;

enum CmsBuilderBlock: string
{
    case Title = 'title';
    case RichEditor = 'editor';
    case ImageText = 'image_text';
    case ImageHeader = 'image_header';
    case IconText = 'icon-text';
    case Box = 'box';
    case Slider = 'slider';
    case Table = 'table';
    case Shop = 'shop';
    case Packages = 'packages';
    case FeatureCards = 'feature-cards';
    case TestimonialCards = 'testimonial-cards';
    case Tabs = 'tabs';
    case Section = 'section';
    case SimpleImage = 'simple_image';
    case VideoText = 'video_text';
    case TwoColumnText = 'two_column_text';
    case SimpleButton = 'simple_button';
    case Faq = 'faq';


    public function getComponentName(): string
    {
        return match ($this) {
            self::RichEditor => 'cms.blocks.rich-text',
            self::Title => 'cms.blocks.title',
            self::ImageText => 'cms.blocks.image-text',
            self::ImageHeader => 'cms.blocks.image-header',
            self::IconText => 'cms.blocks.icon-text',
            self::Box => 'cms.blocks.box',
            self::Slider => 'cms.blocks.slider',
            self::Table => 'cms.blocks.table',
            self::Shop => 'cms.blocks.shop',
            self::Packages => 'cms.blocks.packages',
            self::FeatureCards => 'cms.blocks.feature-cards',
            self::TestimonialCards => 'cms.blocks.testimonial-cards',
            self::Tabs => 'cms.blocks.tabs',
            self::Section => 'cms.blocks.section',
            self::SimpleImage => 'cms.blocks.simple-image',
            self::VideoText => 'cms.blocks.video-text',
            self::TwoColumnText => 'cms.blocks.two-column-text',
            self::SimpleButton => 'cms.blocks.simple-button',
            self::Faq => 'cms.blocks.faq',
            default => '',
        };
    }
}
