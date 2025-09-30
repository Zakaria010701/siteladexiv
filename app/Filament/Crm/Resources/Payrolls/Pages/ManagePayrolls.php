<?php

namespace App\Filament\Crm\Resources\Payrolls\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Crm\Resources\Payrolls\PayrollResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePayrolls extends ManageRecords
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
