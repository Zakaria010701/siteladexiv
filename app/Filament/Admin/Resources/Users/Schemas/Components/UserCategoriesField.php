<?php

namespace App\Filament\Admin\Resources\Users\Schemas\Components;

use App\Models\Service;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Field;

class UserCategoriesField {

    public static function make(): Field
    {
        return CheckboxList::make('categories')
            ->live()
            ->partiallyRenderComponentsAfterStateUpdated(['services'])
            ->relationship('categories', 'name')
            ->afterStateUpdated(function ($set, $state) {
                $services = Service::where('category_id', $state)->pluck('id')->toArray();
                $set('services', $services);
            })
            ->visibleJs(<<<'JS'
                $get('is_provider')
            JS);
    }

}
