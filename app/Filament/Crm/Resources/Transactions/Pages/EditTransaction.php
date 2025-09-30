<?php

namespace App\Filament\Crm\Resources\Transactions\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Crm\Resources\Transactions\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
