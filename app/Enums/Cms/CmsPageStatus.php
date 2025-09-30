<?php

namespace App\Enums\Cms;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum CmsPageStatus: string implements HasColor, HasLabel, HasIcon
{
    case Draft = 'draft';
    case Published = 'published';


    public function getColor(): string|array|null
    {
        return match($this) {
            self::Draft => 'gray',
            self::Published => 'success',
        };
    }

    public function getIcon(): string|BackedEnum|null
    {
        return match($this) {
            self::Draft => Heroicon::Pencil,
            self::Published => Heroicon::CheckCircle,
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return __("cms.page.status.$this->value");
    }
}
