<?php

namespace App\Filament\Admin\Resources\Users\Schemas\Components;

use App\Models\Service;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Field;

class UserConsultationCategoriesField {

    public static function make(): Field
    {
        return CheckboxList::make('consultation_categories')
            ->relationship('consultationCategories', 'name')
            ->visibleJs(<<<'JS'
                $get('is_provider')
            JS);
    }

}
