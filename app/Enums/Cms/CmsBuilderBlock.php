<?php

namespace App\Enums\Cms;

use Filament\Forms\Components\RichEditor;

enum CmsBuilderBlock: string
{
    case Title = 'title';
    case RichEditor = 'editor';
    case ImageText = 'image_text';
    case IconText = 'icon-text';
    case Box = 'box';
    case Slider = 'slider';
    case Table = 'table';
    case Shop = 'shop';

    public function getComponentName(): string
    {
        return match ($this) {
            self::RichEditor => 'cms.blocks.rich-text',
            self::Title => 'cms.blocks.title',
            self::ImageText => 'cms.blocks.image-text',
            self::IconText => 'cms.blocks.icon-text',
            self::Box => 'cms.blocks.box',
            self::Slider => 'cms.blocks.slider',
            self::Table => 'cms.blocks.table',
            self::Shop => 'cms.blocks.shop',
            default => '',
        };
    }
}
