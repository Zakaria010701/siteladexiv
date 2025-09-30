<?php

namespace App\Filament\Admin\Resources\Availabilities\Schemas\Components;

use App\Forms\Components\FusedMorphToSelect;
use App\Models\Branch;
use App\Models\SystemResource;
use App\Models\User;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;

class AvailabilityPlanableSelect
{
    public static function make(): MorphToSelect
    {
        return FusedMorphToSelect::make('planable')
            ->label(__('availability.planable'))
            ->required()
            ->hiddenOn('edit')
            ->types([
                Type::make(User::class)
                    ->titleAttribute('name'),
                Type::make(Branch::class)
                    ->titleAttribute('name'),
                Type::make(SystemResource::class)
                    ->titleAttribute('name'),
            ]);
    }
}
