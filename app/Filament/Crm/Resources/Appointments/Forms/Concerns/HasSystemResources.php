<?php

namespace App\Filament\Crm\Resources\Appointments\Forms\Concerns;

use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\SystemResource;
use App\Models\SystemResourceType;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

trait HasSystemResources
{
    private function getSystemResourceFieldset(): Fieldset
    {

        return Fieldset::make('systemResources')
            ->schema(function (Get $get) {
                $start = CarbonImmutable::parse($get('start'));
                $resourceTypes = Cache::rememberForever("appointment_resource_types", fn () => SystemResourceType::query()
                    ->with([
                        'systemResources' => [
                            'branchDependencies',
                            'categoryDependencies',
                            'roomDependencies',
                            'userDependencies',
                        ],
                        'systemResourceTypeDependencies',
                    ])
                    ->where('show_in_appointment', true)
                    ->orderBy('name')
                    ->get());

                $resourceTypes->load([
                    'systemResources.availabilities' =>  fn (Builder $query) => $query
                        ->where('start_date', '<=', $start)
                        ->where(fn (Builder $query) => $query->whereNull('end_date')->orWhere('end_date', '>=', $start))
                ]);

                $dependencies = $resourceTypes->mapWithKeys(fn (SystemResourceType $type) => [
                    $type->name => $get($type->name)
                ])->toArray();

                return $resourceTypes->map(fn (SystemResourceType $type) => Select::make($type->name)
                    ->label($type->name)
                    ->live(onBlur: true)
                    ->required($type->is_required)
                    ->searchable()
                    ->preload()
                    ->multiple($type->allow_multiple)
                    ->relationship(
                        name: 'systemResources',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('system_resource_type_id', $type->id)
                    )

                    ->disableOptionWhen(fn (int $value) => !in_array($value, $type->getAvailableSystemResources(
                        $start,
                        $get('branch_id'),
                        $get('category_id'),
                        $get('user_id'),
                        $get('room_id'),
                        $dependencies
                    )->pluck('id')->toArray()))
                )->toArray();
            });
    }
}
