<?php

namespace App\Filament\Crm\Resources\CustomerCredits\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Crm\Resources\CustomerCredits\CustomerCreditResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomerCredit extends EditRecord
{

    protected static string $resource = CustomerCreditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
