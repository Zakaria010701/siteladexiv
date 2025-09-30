<?php

namespace App\Filament\Crm\Resources\Appointments\RelationManagers;

use App\Filament\Crm\Concerns\HasContractsRelation;
use Filament\Resources\RelationManagers\RelationManager;

class CustomerContractsRelationManager extends RelationManager
{
    use HasContractsRelation;

    protected static string $relationship = 'customerContracts';
}
