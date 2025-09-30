<?php

namespace App\Filament\Admin\Resources\Users\Schemas\Components;

use Filament\Forms\Components\Toggle;

class UserShowInFrontendToggle
{
    public static function make(): Toggle
    {
        return Toggle::make('show_in_frontend')
            ->label(__('user.show_in_frontend'))
            ->helperText(__('user.show_in_frontend_helper'))
            ->visibleJs(<<<'JS'
                $get('is_provider')
            JS);
    }
}
