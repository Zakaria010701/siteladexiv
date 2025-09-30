<?php

namespace App\Filament\Crm\Resources\Vouchers\Pages;

use App\Filament\Crm\Resources\Vouchers\VoucherResource;
use App\Models\Voucher;
use Filament\Resources\Pages\CreateRecord;

class CreateVoucher extends CreateRecord
{
    protected static string $resource = VoucherResource::class;

    protected function afterFill()
    {
        $this->data['voucher_nr'] = Voucher::max('voucher_nr') + 1;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['voucher_nr'] = Voucher::max('voucher_nr') + 1;

        return $data;
    }
}
