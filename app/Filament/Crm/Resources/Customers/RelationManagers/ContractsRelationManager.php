<?php

namespace App\Filament\Crm\Resources\Customers\RelationManagers;

use Illuminate\Database\Eloquent\Model;
use App\Filament\Crm\Concerns\HasContractsRelation;
use Filament\Resources\RelationManagers\RelationManager;

class ContractsRelationManager extends RelationManager
{
    use HasContractsRelation;

    protected static string $relationship = 'contracts';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Contracts');
    }

    public static function getModelLabel(): string
    {
        return __('Contract');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Contracts');
    }
}
