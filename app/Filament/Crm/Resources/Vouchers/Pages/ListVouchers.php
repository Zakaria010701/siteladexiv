<?php

namespace App\Filament\Crm\Resources\Vouchers\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Crm\Resources\Vouchers\VoucherResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVouchers extends ListRecords
{
    protected static string $resource = VoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
