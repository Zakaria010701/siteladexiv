<?php

namespace App\Filament\Crm\Resources\Transactions\Pages;

use App\Filament\Crm\Resources\Transactions\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;
}
