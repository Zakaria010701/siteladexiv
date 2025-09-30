<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Room;
use App\Models\Service;
use App\Models\SystemResource;
use App\Models\SystemResourceType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemResourceType::upsert([[
            'id' => 1,
            'name' => 'Laser',
            'show_in_appointment' => 1,
            'is_required' => 1,
            'allow_multiple' => 0,
            'depends_on_branch' => 1,
            'depends_on_room' => 1,
            'depends_on_category' => 1,
            'depends_on_user' => 0,
            'depends_on_availability' => 0,
        ]], ['id']);

        $resources = collect([
            [
                'id' => 1,
                'system_resource_type_id' => 1,
                'name' => 'ATV Tattoo',
                'branches' => [1, 2],
                'rooms' => [1, 2, 3, 4, 5, 6, 7, 8, 10, 12],
                'categories' => [4, 6],
            ],
            [
                'id' => 2,
                'system_resource_type_id' => 1,
                'name' => 'Pico',
                'branches' => [1],
                'rooms' => [1, 2, 3, 4, 5, 6, 8],
                'categories' => [3, 4, 6, 9, 17],
            ],
            [
                'id' => 3,
                'system_resource_type_id' => 1,
                'name' => 'Alexandrit',
                'branches' => [1, 2],
                'rooms' => [1, 2, 3, 4, 5, 6, 7, 8, 10, 12],
                'categories' => [1, 3],
            ],
            [
                'id' => 4,
                'system_resource_type_id' => 1,
                'name' => 'Microneedle',
                'branches' => [1, 2],
                'rooms' => [1, 2, 3, 4, 5, 6, 7, 8, 10, 12],
                'categories' => [3],
            ],
            [
                'id' => 5,
                'system_resource_type_id' => 1,
                'name' => 'CO2',
                'branches' => [1],
                'rooms' => [1, 2, 3, 4, 5, 6, 8],
                'categories' => [3, 6, 9, 17],
            ],
            [
                'id' => 6,
                'system_resource_type_id' => 1,
                'name' => 'Nordlys',
                'branches' => [1],
                'rooms' => [1, 2, 3, 4, 5, 6, 8],
                'categories' => [3, 6, 9, 17],
            ],
            [
                'id' => 7,
                'system_resource_type_id' => 1,
                'name' => 'YAG Laser',
                'branches' => [1],
                'rooms' => [1, 2, 3, 4, 5, 6, 8],
                'categories' => [3, 6, 9, 17],
            ],
            [
                'id' => 8,
                'system_resource_type_id' => 1,
                'name' => 'Hydrafacial',
                'branches' => [3],
                'rooms' => [9],
                'categories' => [7],
            ],
            [
                'id' => 9,
                'system_resource_type_id' => 1,
                'name' => 'Kriolypolyse',
                'branches' => [1],
                'rooms' => [1, 2, 3, 4, 5, 6, 8],
                'categories' => [8],
            ],
            [
                'id' => 10,
                'system_resource_type_id' => 1,
                'name' => 'Vanqish Me',
                'branches' => [1],
                'rooms' => [1, 2, 3, 4, 5, 6, 8],
                'categories' => [8],
            ],
        ]);

        SystemResource::upsert($resources->map(fn (array $resource) => [
            'id' => $resource['id'],
            'system_resource_type_id' => $resource['system_resource_type_id'],
            'name' => $resource['name'],
        ])->toArray(), ['id']);

        SystemResource::whereIn('id', $resources->pluck('id'))
            ->get()
            ->each(function (SystemResource $resource) use ($resources) {
                $seed = $resources->where('id', $resource->id)->first();
                $resource->branchDependencies()->syncWithoutDetaching($seed['branches']);
                $resource->roomDependencies()->syncWithoutDetaching($seed['rooms']);
                $resource->categoryDependencies()->syncWithoutDetaching($seed['categories']);
                $resource->serviceDependencies()->syncWithoutDetaching(Service::whereIn('category_id', $seed['categories'])->pluck('id'));
            });
    }
}
