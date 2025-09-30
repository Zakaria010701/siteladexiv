<?php

namespace App\Filament\Crm\Resources\Banks\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Crm\Resources\Banks\BankResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBank extends EditRecord
{
    protected static string $resource = BankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
