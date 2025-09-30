<?php

namespace App\Http\Resources;

use App\Models\SystemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalendarOpeningApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'start' => $this->start,
            'end' => $this->end,
            'room' => [
                'id' => $this->room->id,
                'name' => $this->room->name,
            ],
            'branch' => [
                'id' => $this->room->branch->id,
                'name' => $this->room->branch->name,
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->full_name,
            ],
            'availability' => [
                'id' => $this->availability->availability_id,
                'event_id' => $this->availability->id,
                'event_type' => $this->availability::class,
            ],
            'resources' => collect($this->resources)->map(fn (SystemResource $resource) => [
                'id' => $resource->id,
                'name' => $resource->name,
                'type' => [
                    'id' => $resource->systemResourceType->id,
                    'name' => $resource->systemResourceType->name,
                ],
            ])->toArray(),
        ];
    }
}
