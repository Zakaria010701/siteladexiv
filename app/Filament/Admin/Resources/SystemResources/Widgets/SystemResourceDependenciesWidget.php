<?php

namespace App\Filament\Admin\Resources\SystemResources\Widgets;

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

class SystemResourceDependenciesWidget extends BaseResourceDependenciesWidget
{
    public int $systemResourceTypeId;

    public string $titleAttribute = 'name';

    #[Computed]
    public function records(): Collection
    {
        return SystemResource::with([
            'branchDependencies:id',
            'userDependencies:id',
            'categoryDependencies:id',
            'serviceDependencies:id',
        ])->where('system_resource_type_id', $this->systemResourceTypeId)->get(['id', 'name']);
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
        return SystemResourceResource::getUrl('edit', ['record' => $record]);
    }

    #[Computed]
    public function relationships(): array
    {
        $relationships = [
            [
                'label' => __('Branch'),
                'titleAttribute' => 'name',
                'records' => Branch::all(['id', 'name']),
                'relation' => 'branchDependencies',
                'relatedIds' => $this->records->mapWithKeys(fn (SystemResource $record) => [$record->id => $record->branchDependencies->pluck('id')->toArray()])->toArray(),
                'resource' => BranchResource::class,
                'route' => 'edit'
            ],
            [
                'label' => __('User'),
                'titleAttribute' => 'name',
                'records' => User::all(['id', 'name']),
                'relation' => 'userDependencies',
                'relatedIds' => $this->records->mapWithKeys(fn (SystemResource $record) => [$record->id => $record->userDependencies->pluck('id')->toArray()])->toArray(),
                'resource' => UserResource::class,
                'route' => 'edit'
            ],
            [
                'label' => __('Category'),
                'titleAttribute' => 'name',
                'records' => Category::all(['id', 'name']),
                'relation' => 'categoryDependencies',
                'relatedIds' => $this->records->mapWithKeys(fn (SystemResource $record) => [$record->id => $record->categoryDependencies->pluck('id')->toArray()])->toArray(),
                'resource' => CategoryResource::class,
                'route' => 'index'
            ],
            [
                'label' => __('Service'),
                'titleAttribute' => 'name',
                'records' => Service::all(['id', 'name']),
                'relation' => 'serviceDependencies',
                'relatedIds' => $this->records->mapWithKeys(fn (SystemResource $record) => [$record->id => $record->serviceDependencies->pluck('id')->toArray()])->toArray(),
                'resource' => ServiceResource::class,
                'route' => 'edit'
            ],
        ];

        return $relationships;
    }
}
