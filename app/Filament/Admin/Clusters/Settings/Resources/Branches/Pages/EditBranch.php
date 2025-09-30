<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\Branches\Pages;

use App\Filament\Admin\Clusters\Settings\Resources\Branches\BranchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBranch extends EditRecord
{
    protected static string $resource = BranchResource::class;
}
