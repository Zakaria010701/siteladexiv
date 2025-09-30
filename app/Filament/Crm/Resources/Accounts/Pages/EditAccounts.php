<?php

namespace App\Filament\Crm\Resources\Accounts\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Crm\Resources\Accounts\AccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAccounts extends EditRecord
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
