<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\Services\Widgets;

use App\Filament\Admin\Clusters\Settings\Resources\Branches\BranchResource;
use App\Filament\Admin\Clusters\Settings\Resources\Rooms\RoomResource;
use App\Filament\Admin\Clusters\Settings\Resources\Services\ServiceResource;
use App\Filament\Admin\Resources\SystemResources\SystemResourceResource;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Filament\Admin\Widgets\BaseResourceDependenciesWidget;
use App\Models\Branch;
use App\Models\Room;
use App\Models\Service;
use App\Models\SystemResourceType;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

class ServiceDependenciesWidget extends BaseResourceDependenciesWidget
{
    public string $titleAttribute = 'name';

    #[Computed]
    public function records(): Collection
    {
        return Service::with(['branches:id', 'rooms:id', 'users:id', 'systemResources:id'])->get(['id', 'name']);
    }

    #[Computed]
    public function record_count(): int
    {
        return $this->records->count();
    }

    public function model(): string
    {
        return Service::class;
    }

    public function getRecordUrl(Model $record): string
    {
        return ServiceResource::getUrl('edit', ['record' => $record]);
    }

    #[Computed]
    public function relationships(): array
    {
        $relationships = [
            [
                'label' => __('Branch'),
                'titleAttribute' => 'name',
                'records' => Branch::all(['id', 'name']),
                'relation' => 'branches',
                'relatedIds' => $this->records->mapWithKeys(fn (Service $record) => [$record->id => $record->branches->pluck('id')->toArray()])->toArray(),
                'resource' => BranchResource::class,
                'route' => 'edit'
            ],
            [
                'label' => __('Room'),
                'titleAttribute' => 'name',
                'records' => Room::all(['id', 'name']),
                'relation' => 'rooms',
                'relatedIds' => $this->records->mapWithKeys(fn (Service $record) => [$record->id => $record->rooms->pluck('id')->toArray()])->toArray(),
                'resource' => RoomResource::class,
                'route' => 'index'
            ],
            [
                'label' => __('User'),
                'titleAttribute' => 'name',
                'records' => User::all(['id', 'name']),
                'relation' => 'users',
                'relatedIds' => $this->records->mapWithKeys(fn (Service $record) => [$record->id => $record->users->pluck('id')->toArray()])->toArray(),
                'resource' => UserResource::class,
                'route' => 'edit'
            ],
        ];


        $relatedResources = $this->records->mapWithKeys(fn (Service $record) => [$record->id => $record->systemResources->pluck('id')->toArray()])->toArray();
        $resources = SystemResourceType::with('systemResources:id,system_resource_type_id,name')->get(['id', 'name'])
            ->map(function (SystemResourceType $type) use ($relatedResources) {
                return [
                    'label' => $type->name,
                    'titleAttribute' => 'name',
                    'records' => $type->systemResources,
                    'relation' => 'systemResources',
                    'relatedIds' => $relatedResources,
                    'resource' => SystemResourceResource::class,
                    'route' => 'edit',
                ];
            })->toArray();

        $relationships = array_merge($relationships, $resources);

        return $relationships;
    }
}
