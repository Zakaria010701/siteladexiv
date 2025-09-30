<?php

namespace App\Filament\Admin\Resources\Users\RelationManagers;

use App\Enums\TimeStep;
use App\Enums\Weekday;
use App\Filament\Admin\Concerns\ManagesAvailabilitiesRelation;
use App\Filament\Admin\Resources\Availabilities\AvailabilityResource;
use App\Forms\Components\TableRepeater;
use App\Models\Availability;
use App\Models\AvailabilityType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AvailabilitiesRelationManager extends RelationManager
{
    use ManagesAvailabilitiesRelation;

    protected static string $relationship = 'availabilities';
}
