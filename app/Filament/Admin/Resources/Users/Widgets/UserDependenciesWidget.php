<?php

namespace App\Filament\Admin\Resources\Users\Widgets;

use App\Filament\Admin\Clusters\Settings\Resources\Branches\BranchResource;
use App\Filament\Admin\Clusters\Settings\Resources\Categories\CategoryResource;
use App\Filament\Admin\Clusters\Settings\Resources\Rooms\RoomResource;
use App\Filament\Admin\Clusters\Settings\Resources\Services\ServiceResource;
use App\Filament\Admin\Resources\SystemResources\SystemResourceResource;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Filament\Admin\Widgets\BaseResourceDependenciesWidget;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Room;
use App\Models\Service;
use App\Models\SystemResource;
use App\Models\SystemResourceType;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

class UserDependenciesWidget extends BaseResourceDependenciesWidget
{
    public string $titleAttribute = 'name';

    #[Computed]
    public function records(): Collection
    {
        return User::with([
            'branches:id',
            'categories:id',
            'services:id',
            'systemResources:id',
        ])->get(['id', 'name']);
    }

    #[Computed]
    public function record_count(): int
    {
        return $this->records->count();
    }

    public function model(): string
    {
        return User::class;
    }

    public function getRecordUrl(Model $record): string
    {
        return UserResource::getUrl('edit', ['record' => $record]);
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
                'relatedIds' => $this->records->mapWithKeys(fn (User $record) => [$record->id => $record->branches->pluck('id')->toArray()])->toArray(),
                'resource' => BranchResource::class,
                'route' => 'edit'
            ],
            [
                'label' => __('Category'),
                'titleAttribute' => 'name',
                'records' => Category::all(['id', 'name']),
                'relation' => 'categories',
                'relatedIds' => $this->records->mapWithKeys(fn (User $record) => [$record->id => $record->categories->pluck('id')->toArray()])->toArray(),
                'resource' => CategoryResource::class,
                'route' => 'index'
            ],
            [
                'label' => __('Service'),
                'titleAttribute' => 'name',
                'records' => Service::all(['id', 'name']),
                'relation' => 'services',
                'relatedIds' => $this->records->mapWithKeys(fn (User $record) => [$record->id => $record->services->pluck('id')->toArray()])->toArray(),
                'resource' => ServiceResource::class,
                'route' => 'edit'
            ],
        ];


        $relatedResources = $this->records->mapWithKeys(fn (User $record) => [$record->id => $record->systemResources->pluck('id')->toArray()])->toArray();
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
