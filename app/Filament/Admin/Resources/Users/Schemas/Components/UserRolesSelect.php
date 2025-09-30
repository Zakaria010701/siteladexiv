<?php

namespace App\Filament\Admin\Resources\Users\Schemas\Components;

use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Forms\Components\Select;

class UserRolesSelect {

    public static function make(): Select
    {
        return Select::make('roles')
            ->required()
            ->multiple()
            ->preload()
            ->hidden(fn (string $operation) => $operation != 'create' && !UserResource::can('admin'))
            ->relationship('roles', 'name');
    }
}
