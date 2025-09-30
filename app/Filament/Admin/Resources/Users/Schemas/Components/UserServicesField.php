<?php

namespace App\Filament\Admin\Resources\Users\Schemas\Components;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Field;

class UserServicesField {

    public static function make(): Field
    {
        return CheckboxList::make('services')
            ->relationship('services', 'name')
            ->columns(3)
            ->columnSpanFull()
            ->visibleJs(<<<'JS'
                $get('is_provider')
            JS);
    }

}
