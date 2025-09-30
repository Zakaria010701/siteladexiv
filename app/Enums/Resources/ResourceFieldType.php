<?php

namespace App\Enums\Resources;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum ResourceFieldType: string implements HasLabel
{
    case Text = 'text';
    case Number = 'number';
    case Date = 'date';
    case DateTime = 'date-time';
    case Select = 'select';
    case Toggle = 'toggle';
    case Checkboxes = 'checkboxes';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->title()->toString());
    }

    public function hasOptions(): bool
    {
        return match($this) {
            self::Select, self::Toggle, self::Checkboxes => true,
            default => false,
        };
    }
}
