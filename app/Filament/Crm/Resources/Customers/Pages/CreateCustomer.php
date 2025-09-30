<?php

namespace App\Filament\Crm\Resources\Customers\Pages;

use App\Filament\Crm\Resources\Customers\CustomerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{

    protected static string $resource = CustomerResource::class;
}
