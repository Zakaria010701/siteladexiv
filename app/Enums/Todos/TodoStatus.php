<?php

namespace App\Enums\Todos;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum TodoStatus: string implements HasColor, HasLabel
{
    case NotDone = 'not-done';
    case Done = 'done';
    case Overdue = 'overdue';

    public function getLabel(): ?string
    {
        return __(Str::of($this->value)->replace('-', ' ')->title()->toString());
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::NotDone => 'primary',
            self::Done => 'success',
            self::Overdue => 'danger',
        };
    }
}
