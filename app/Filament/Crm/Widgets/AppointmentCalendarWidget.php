<?php

namespace App\Filament\Crm\Widgets;

use App\Filament\Crm\Resources\Appointments\Concerns\HasCancelAppointmentAction;
use App\Filament\Crm\Resources\Appointments\Concerns\HasApproveAppointmentAction;
use App\Filament\Crm\Widgets\Concerns\Calendar\HasAvailabilityEvents;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use App\Enums\Appointments\AppointmentDeleteReason;
use App\Enums\Appointments\AppointmentStatus;
use App\Enums\Appointments\AppointmentType;
use App\Enums\TimeStep;
use App\Enums\WorkTimes\WorkTimeType;
use App\Filament\Actions\Appointments\CreateAppointmentAction;
use App\Filament\Actions\Appointments\QuickbookAction;
use App\Filament\Actions\Appointments\ResizeAppointmentAction;
use App\Filament\Actions\Appointments\ViewAppointmentAction;
use App\Filament\Actions\Calendar\CreateAction;
use App\Filament\Actions\Calendar\DeleteAction;
use App\Filament\Actions\Calendar\EditAction;
use App\Filament\Actions\ReportBugAction;
use App\Filament\Concerns\Appointments\HasCreateAppointmentWizard;
use App\Filament\Crm\Concerns\HasAppointmentForm;
use App\Filament\Crm\Pages\Dashboard;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use App\Filament\Crm\Resources\Appointments\Forms\AppointmentForm;
use App\Filament\Crm\Resources\WorkTimeGroups\WorkTimeGroupResource;
use App\Filament\Crm\Resources\WorkTimes\WorkTimeResource;
use App\Filament\Widgets\BaseCalendarWidget;
use App\Livewire\Forms\MoveAppointmentAction;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\AvailabilityShift;
use App\Models\Branch;
use App\Models\Room;
use App\Models\RoomBlock;
use App\Models\WorkTime;
use App\Settings\GeneralSettings;
use App\Support\Appointment\AppointmentCalculator;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

use function PHPUnit\Framework\callback;

class AppointmentCalendarWidget extends BaseCalendarWidget
{
    //use HasAppointmentForm;
    use HasCancelAppointmentAction;
    use HasApproveAppointmentAction;
    use HasAvailabilityEvents;
    use HasCreateAppointmentWizard;

    public Model|string|null $model = Appointment::class;

    private Branch $branch;

    public function __construct()
    {
        $this->branch = auth()->user()->currentBranch ?? Branch::first();
    }

    #[On('switched-branch')]
    public function switchedBranch(Branch $branch)
    {
        $this->branch = $branch;
        $this->dispatch('filament-fullcalendar--reload');
    }

    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views on the calendar.
     */
    public function fetchEvents(array $info): array
    {
        $branch = auth()->user()->current_branch_id;
        $start = CarbonImmutable::parse($info['start']);
        $end = CarbonImmutable::parse($info['end']);
        $events = Appointment::query()
            ->where('branch_id', $branch)
            ->whereBetween('start', [$start, $end])
            ->notCanceled()
            ->get()
            ->map(fn (Appointment $appointment): array => [[
                'id' => $appointment->id,
                'url' => AppointmentResource::getUrl('edit', ['record' => $appointment]),
                'title' => $this->getAppointmentTitle($appointment),
                'resourceId' => $appointment->room_id,
                'start' => $appointment->start,
                'end' => $appointment->end,
                'color' => getColorValue($appointment->getColor(), 600),
                //'textColor' => $appointment->text_color,
                'type' => 'appointment',
            ]])
            /*->push(RoomBlock::query()
                ->whereHas('room', fn (Builder $query) => $query->where('branch_id', $branch))
                ->whereBetween('start_at', [$start, $end])
                ->get()
                ->map(fn (RoomBlock $record) => [
                    'id' => $record->id,
                    'start' => $record->start_at,
                    'end' => $record->end_at,
                    'resourceId' => $record->room_id,
                    'group' => 'roomblock',
                    'color' => '#787878'
                ]))*/
            ->push($this->getAvailabilityEvents($start, $end))
            ->flatten(1)
            ->toArray();

        return $events;
    }

    private function getAppointmentTitle(Appointment $appointment): string
    {
        $start = $appointment->start->format(general()->time_format);

        if ($appointment->type->isRoomBlock()) {
            if ($appointment->start->diffInMinutes($appointment->end) <= general()->default_time_slot) {
                return sprintf("%s %s", $start, $appointment->description);
            }
            return sprintf("%s \n%s - %s", $start, $appointment->description, $appointment->user->name);
        }

        if ($appointment->start->diffInMinutes($appointment->end) <= general()->default_time_slot) {
            return sprintf('%s %s', $start, $appointment->customer->full_name);
        }

        return sprintf("%s (%s)\n%s",
            $start,
            $appointment->category?->short_code,
            $appointment->customer?->full_name ?? ''
        );
    }

    public function fetchResources(array $info): array
    {
        return $this->branch->rooms()
            ->get()
            ->map(fn (Room $record) => [
                'id' => $record->id,
                'title' => $record->name,
            ])
            ->toArray();
    }

    public function getConfig(): array
    {
        return [
            'initialView' => 'resourceTimeGridWeek',
            'resourceOrder' => 'sorting',
            'slotMinTime' => $this->branch->calendar_start_time,
            'slotMaxTime' => $this->branch->calendar_end_time,
            'slotLabelInterval' => calendar()->slot_label_interval,
            'slotDuration' => calendar()->slot_duration,
            'datesAboveResources' => calendar()->dates_above_resources,
            'dayMinWidth' => 100,
            'height' => 'auto',
            'nowIndicator' => calendar()->now_indicator,
            'hiddenDays' => [0],
            'weekends' => false,
            'businessHours' => [
                'daysOfWeek' => array_values($this->branch->open_days),
                'startTime' => $this->branch->calendar_start_time,
                'endTime' => $this->branch->calendar_end_time,
            ],
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'resourceTimeGridWeek,resourceTimeGridDay',
            ],
            'selectConstraint' => 'availability',
            'eventConstraint' => 'availability',
            'slotLabelFormat' => [
                [
                    'hour' => 'numeric',
                    'minute' => '2-digit',
                    'omitZeroMinute' => false,
                    'hour12' => false,
                ],
            ],
        ];
    }

    protected function createAction(): Action
    {
        return CreateAppointmentAction::make()
            ->after(fn () => $this->refreshRecords());
    }

    protected function infoAction(): Action
    {
        return ViewAppointmentAction::make()
            ->record(fn () => $this->getRecord());
    }

    protected function editRoomblockAction(): Action
    {
        return EditAction::make('editRoomblock')
            ->model(Appointment::class)
            ->schema(fn (Schema $schema) => AppointmentForm::compact($schema))
            ->modalHeading(__('filament-actions::edit.single.modal.heading', ['label' => __('Room Block')]))
            ->extraModalFooterActions([
                DeleteAction::make(),
            ]);
    }

    public function onEventClick(array $event, ?string $modifier = null): void
    {
        $this->dispatch('filament-fullcalendar--disable-move');
        if (empty($event['extendedProps']['type'])) {
            return;
        }
        match ($event['extendedProps']['type']) {
            'workplan' => $this->onWorkTimeClick($event, $modifier),
            'appointment' => $this->onAppointmentClick($event, $modifier),
            'availability' => $this->onAvailabilityClick($event, $modifier),
            'availability-group' => $this->onAvailabilityGroupClick($event, $modifier),
            default => null,
        };
    }

    public function onAppointmentClick(array $event, ?string $modifier = null): void
    {
        if ($this->getModel()) {
            $this->record = $this->resolveRecord($event['id']);
        }

        if ($this->getRecord()->type->isRoomBlock()) {
            $this->mountAction('editRoomblock', [
                'type' => 'click',
                'event' => $event,
            ]);
        } else {
            $this->mountAction('info', [
                'type' => 'click',
                'event' => $event,
            ]);
        }
    }

    public function onEventDrop(array $event, array $oldEvent, array $relatedEvents, array $delta, ?array $oldResource, ?array $newResource): bool
    {
        $this->dispatch('filament-fullcalendar--disable-move');

        return match ($event['extendedProps']['group']) {
            'appointment' => $this->onAppointmentDrop($event, $oldEvent, $relatedEvents, $delta, $oldResource, $newResource),
            default => true,
        };
    }

    public function onAppointmentDrop(array $event, array $oldEvent, array $relatedEvents, array $delta, ?array $oldResource, ?array $newResource): bool
    {
        $this->record = $this->resolveRecord($event['id']);

        $this->dispatch('filament-fullcalendar--reload');

        $this->mountAction('moveAppointment', [
            'start' => $event['start'],
            'end' => $event['end'],
            'room_id' => $newResource['id'] ?? $this->getRecord()->room_id,
        ]);

        return false;
    }

    protected function moveAppointmentAction(): Action
    {
        return MoveAppointmentAction::make()
            ->after(fn () => $this->dispatch('filament-fullcalendar--reload'));
    }


    public function onEventResize(array $event, array $oldEvent, array $relatedEvents, array $startDelta, array $endDelta): bool
    {
        return match ($event['extendedProps']['group']) {
            'appointment' => $this->onAppointmentResize($event, $oldEvent, $relatedEvents, $startDelta, $endDelta),
            default => true,
        };
    }

    protected function onAppointmentResize(array $event, array $oldEvent, array $relatedEvents, array $startDelta, array $endDelta): bool
    {
        $this->record = $this->resolveRecord($event['id']);

        $this->mountAction('resizeAppointment', [
            'start' => $event['start'],
            'end' => $event['end'],
            'room_id' => $newResource['id'] ?? $this->getRecord()->room_id,
        ]);

        return true;
    }

    protected function resizeAppointmentAction(): Action
    {
        return ResizeAppointmentAction::make()
            ->after(fn () => $this->dispatch('filament-fullcalendar--reload'));
    }

    public function onSlotSelect(string $start, ?string $end, bool $allDay, ?array $view, ?array $resource, ?bool $move = false): void
    {
        $this->dispatch('filament-fullcalendar--disable-move');
        [$start, $end] = $this->calculateTimezoneOffset($start, $end, $allDay);

        if ($allDay) {
            return;
        }

        if ($move) {
            /** @var Appointment */
            $record = $this->getRecord();
            if (empty($record)) {
                return;
            }
            $this->mountAction('moveAppointment', [
                'start' => $start,
                'end' => Carbon::parse($start)->addMinutes($record->duration),
                'room_id' => $resource['id'] ?? $this->getRecord()->room_id,
            ]);

            return;
        }

        $this->mountAction('create', [
            'type' => 'select',
            'start' => $start,
            'end' => $end,
            'allDay' => false,
            'resource' => $resource,
        ]);
    }

    public function onDateClick(string $start, ?string $end, bool $allDay, ?array $view, ?array $resource)
    {
        $this->dispatch('filament-fullcalendar--disable-move');
        if (! $allDay) {
            return;
        }

        $this->mountAction('createAvailability', [
            'type' => 'select',
            'start' => $start,
            'end' => $end,
            'allDay' => true,
            'resource' => $resource,
        ]);

        /*if (module()->inactive('work_time')) {
            return;
        }

        $this->mountAction('createWorkTime', [
            'type' => 'select',
            'start' => $start,
            'end' => $end,
            'allDay' => true,
            'resource' => $resource,
        ]);*/
    }

    public function onWorkTimeClick(array $event, ?string $modifier = null): void
    {
        $this->record = WorkTime::findOrFail($event['id']);

        $this->mountAction('editWorkTime', [
            'type' => 'click',
            'event' => $event,
        ]);
    }

    protected function createWorkTimeAction(): Action
    {
        return CreateAction::make('createWorkTime')
            ->model(WorkTime::class)
            ->authorize(WorkTimeResource::canCreate())
            ->schema(fn (Schema $schema): Schema => WorkTimeResource::form($schema))
            ->fillForm(fn (array $arguments) => [
                'date' => $arguments['start'],
                'start' => $this->branch->calendar_start_time,
                'end' => $this->branch->calendar_end_time,
                'branch_id' => $this->branch->id,
                'user_id' => auth()->id(),
                'room_id' => $arguments['resource']['id'] ?? null,
                'type' => WorkTimeType::Provider->value,
                'repeat_step' => TimeStep::None->value,
                'repeat_every' => 1,
            ])
            ->before(function (CreateAction $action, array $data) {
                $start = Carbon::parse($data['date'])->setTimeFromTimeString($data['start']);
                $end = Carbon::parse($data['date'])->setTimeFromTimeString($data['end']);
                /** @var null|WorkTime */
                $overlapp = WorkTime::query()
                    ->where('start', '<=', $end)
                    ->where('end', '>=', $start)
                    ->where('room_id', $data['room_id'])
                    ->where('type', $data['type'])
                    ->first();

                if(empty($overlapp)) {
                    return;
                }

                Notification::make()
                    ->warning()
                    ->color('warning')
                    ->title(__('Overlapp detected'))
                    ->body(__('Overlapps with :overlapp', ['overlapp' => $overlapp->title]))
                    ->send();

                $action->halt();
            })
            ->using(fn (array $data): Model => WorkTimeResource::createUsing($data))
            ->modalHeading(__('filament-actions::create.single.modal.heading', ['label' => __('WorkTime')]));
    }

    protected function editWorkTimeAction(): Action
    {
        return EditAction::make('editWorkTime')
            ->model(WorkTime::class)
            ->authorize(fn (Model $record): bool => WorkTimeResource::canEdit($record))
            ->schema(fn (Schema $schema): Schema => WorkTimeResource::form($schema))
            ->modalHeading(__('filament-actions::edit.single.modal.heading', ['label' => __('WorkTime')]))
            ->extraModalFooterActions(function (WorkTime $record) {
                $actions = [];
                if (isset($record->workTimeGroup)) {
                    $actions[] = Action::make('editGroup')
                        ->label(__('Edit Group'))
                        ->url(WorkTimeGroupResource::getUrl('edit', ['record' => $record->workTimeGroup]));
                }

                $actions[] = DeleteAction::make();

                return $actions;
            });
    }
}
