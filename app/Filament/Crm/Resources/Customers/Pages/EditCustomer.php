<?php

namespace App\Filament\Crm\Resources\Customers\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Filament\Actions\Customer\MergeAction;
use App\Filament\Actions\ReportBugAction;
use App\Filament\Crm\Concerns\CheckCustomerValid;
use App\Filament\Crm\Resources\Customers\CustomerResource;
use App\Models\Customer;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    use CheckCustomerValid;

    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            MergeAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
            ReportBugAction::make()
                ->reportUrl($this->getUrl(['record' => $this->getRecord()])),
        ];
    }

    protected function afterFill(): void
    {
        $customer = $this->getRecord();
        if ($customer instanceof Customer) {
            $this->checkCustomerValid($customer);
        }
    }
}
