<?php

namespace App\Filament\Admin\Resources\Users\Schemas\Components;

use Filament\Forms\Components\Toggle;

class UserIsProviderToggle
{
    public static function make(): Toggle
    {
        return Toggle::make('is_provider')
            ->label(__('user.is_provider'))
            ->helperText(__('user.is_provider_helper'));
    }
}
