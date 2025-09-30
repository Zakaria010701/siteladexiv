<?php

namespace App\Filament\Admin\Resources\Users\Schemas\Components;

use App\Models\Service;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Field;

class UserBranchesField {

    public static function make(): Field
    {
        return CheckboxList::make('branches')
            ->relationship('branches', 'name');
    }

}
