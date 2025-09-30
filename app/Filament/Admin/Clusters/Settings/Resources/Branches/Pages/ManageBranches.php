<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\Branches\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Clusters\Settings\Resources\Branches\BranchResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBranches extends ManageRecords
{
    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
