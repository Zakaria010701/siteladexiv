<?php

namespace App\Filament\Crm\Resources\CustomerCredits\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Crm\Resources\CustomerCredits\CustomerCreditResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomerCredits extends ListRecords
{
    protected static string $resource = CustomerCreditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
