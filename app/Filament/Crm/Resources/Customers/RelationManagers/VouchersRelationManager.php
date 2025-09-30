<?php

namespace App\Filament\Crm\Resources\Customers\RelationManagers;

use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Schema;
use App\Filament\Crm\Resources\Vouchers\VoucherResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class VouchersRelationManager extends RelationManager
{
    protected static string $relationship = 'vouchers';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Vouchers');
    }

    public static function getModelLabel(): string
    {
        return __('Voucher');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Vouchers');
    }

    public function form(Schema $schema): Schema
    {
        return VoucherResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return VoucherResource::table($table);
    }
}
