<?php

namespace App\Filament\Crm\Resources\WorkTimeGroups\Pages;

use App\Enums\TimeStep;
use App\Filament\Crm\Resources\WorkTimeGroups\WorkTimeGroupResource;
use App\Models\WorkTimeGroup;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkTimeGroup extends CreateRecord
{
    protected static string $resource = WorkTimeGroupResource::class;

    protected function handleRecordCreation(array $data): WorkTimeGroup
    {
        /** @var WorkTimeGroup $group */
        $group = WorkTimeGroup::create($data);

        $step = TimeStep::from($data['repeat_step']);
        $period = $step->getInterval($data['repeat_every'])->toPeriod($data['repeat_from'], $data['repeat_till']);

        foreach ($period as $date) {
            $startT = $date->copy()->setTimeFromTimeString($data['start']);
            $endT = $date->copy()->setTimeFromTimeString($data['end']);
            $group->workTimes()->create([
                'user_id' => $data['user_id'],
                'room_id' => $data['room_id'],
                'branch_id' => $data['branch_id'],
                'type' => $data['type'],
                'start' => $startT,
                'end' => $endT,
            ]);
        }

        return $group;
    }
}
