<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('dependencies')
                ->color('gray')
                ->icon('heroicon-o-arrow-turn-down-right')
                ->url(UserResource::getUrl('dependencies')),
            CreateAction::make(),
        ];
    }
}
