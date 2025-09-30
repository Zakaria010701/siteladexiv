<?php

namespace App\Filament\Crm\Resources\Customers\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Actions\ReportBugAction;
use App\Filament\Crm\Resources\Customers\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ReportBugAction::make()
                ->reportUrl($this->getUrl()),
        ];
    }
}
