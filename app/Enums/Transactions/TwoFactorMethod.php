<?php

namespace App\Enums\Transactions;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum TwoFactorMethod : string implements HasLabel
{
    case None = 'NoPsd2TanMode';

    case ITan = '900';
    case MobileTan = '901';
    case PhotoTan = '902';
    case ChipTanManual = '910';
    case ChipTanOptical = '911';
    case ChipTanUsb = '912';
    case ChipTanQr = '913';
    case SmsTan = '920';
    case PushTan = '921';
    case SecuroGoTan = '944';
    case SecuroGoPlus = '946';

    public function getLabel(): ?string
    {
        return __(Str::of($this->name)->apa()->replace('-', ' ')->title()->toString());
    }
}
