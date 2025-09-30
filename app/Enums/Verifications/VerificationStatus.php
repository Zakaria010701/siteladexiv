<?php

namespace App\Enums\Verifications;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum VerificationStatus : string implements HasColor, HasLabel, HasIcon
{
    case Pass = 'pass';
    case Failure = 'failure';
    case Unverified = 'unverified';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pass => 'success',
            self::Failure => 'danger',
            self::Unverified => 'gray',
        };
    }

    public function getLabel(): ?string
    {
        return __('status.'.$this->value);
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pass => 'heroicon-o-check-circle',
            self::Failure => 'heroicon-o-x-circle',
            self::Unverified => 'heroicon-o-minus-circle',
        };
    }
}
