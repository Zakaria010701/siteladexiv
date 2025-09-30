<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\Branches\Pages;

use App\Filament\Admin\Clusters\Settings\Resources\Branches\BranchResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBranch extends CreateRecord
{
    protected static string $resource = BranchResource::class;
}
