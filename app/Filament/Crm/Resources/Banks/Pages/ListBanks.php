<?php

namespace App\Filament\Crm\Resources\Banks\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Crm\Resources\Banks\BankResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBanks extends ListRecords
{
    protected static string $resource = BankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
