<?php

namespace App\Filament\Admin\Resources\SystemResources\RelationManagers;

use App\Filament\Admin\Concerns\ManagesAvailabilitiesRelation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AvailabilitiesRelationManager extends RelationManager
{
    use ManagesAvailabilitiesRelation;

    protected static string $relationship = 'availabilities';
}
