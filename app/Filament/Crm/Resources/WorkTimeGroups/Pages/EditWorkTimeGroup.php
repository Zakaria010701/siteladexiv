<?php

namespace App\Filament\Crm\Resources\WorkTimeGroups\Pages;

use Filament\Actions\DeleteAction;
use App\Enums\TimeStep;
use App\Filament\Crm\Resources\WorkTimeGroups\WorkTimeGroupResource;
use App\Models\WorkTime;
use App\Models\WorkTimeGroup;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditWorkTimeGroup extends EditRecord
{
    protected static string $resource = WorkTimeGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if ($record instanceof WorkTimeGroup) {
            if ($record->repeat_step->value != $data['repeat_step'] || $record->repeat_every != $data['repeat_every']) {
                $record->workTimes()->delete();
                $step = TimeStep::from($data['repeat_step']);

                $step = TimeStep::from($data['repeat_step']);
                if ($step != TimeStep::None) {
                    $period = $step->getInterval($data['repeat_every'])->toPeriod($data['repeat_from'], $data['repeat_till']);

                    foreach ($period as $date) {
                        $startT = $date->copy()->setTimeFromTimeString($data['start']);
                        $endT = $date->copy()->setTimeFromTimeString($data['end']);
                        $record->workTimes()->create([
                            'user_id' => $data['user_id'],
                            'room_id' => $data['room_id'],
                            'branch_id' => $data['branch_id'],
                            'type' => $data['type'],
                            'start' => $startT,
                            'end' => $endT,
                        ]);
                    }
                }
            } else {
                $record->workTimes->each(function (WorkTime $workTime) use ($data) {
                    $workTime->start = $workTime->start->setTimeFromTimeString($data['start']);
                    $workTime->end = $workTime->end->setTimeFromTimeString($data['end']);
                    $workTime->branch_id = $data['branch_id'];
                    $workTime->user_id = $data['user_id'];
                    $workTime->room_id = $data['room_id'];
                    $workTime->save();
                });
            }
        }

        $record->update($data);

        return $record;
    }
}
