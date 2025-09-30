<?php

namespace App\Filament\Crm\Widgets\Concerns\Calendar;

use Filament\Schemas\Components\Utilities\Get;
use App\Forms\Components\TableRepeater\Header;
use App\Enums\Customers\CustomerOption;
use App\Enums\Invoices\InvoiceStatus;
use App\Enums\Invoices\InvoiceType;
use App\Filament\Actions\Calendar\CreateAction;
use App\Filament\Actions\Calendar\EditAction;
use App\Filament\Widgets\BaseCalendarWidget;
use App\Forms\Components\TableRepeater;
use App\Models\Availability;
use App\Models\AvailabilityException;
use App\Models\AvailabilityType;
use App\Models\Contracts\AvailabilityEvent;
use App\Models\Customer;
use App\Models\SystemResource;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Google\Service\HangoutsChat\DateInput;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait HasAvailabilityEvents
{
    private function getAvailabilityEvents(CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        return $this->getUngroupedAvailabilityEvents($start, $end)
            ->merge($this->getGroupedResourceAvailabilityEvents($start, $end))
            ->merge($this->getGroupedUserAvailabilityEvents($start, $end));

    }

    private function getUngroupedAvailabilityEvents(CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        $types = [];
        if(!calendar()->group_users_in_day_plan) {
            $types[] = User::class;
        }
        if(!calendar()->group_resources_in_day_plan) {
            $types[] = SystemResource::class;
        }
        $callback = fn () => Availability::query()
            ->with([
                'availabilityShifts' => fn (EloquentBuilder $query) => $query
                    ->whereNotNull('room_id')
                    ->whereNotNull('start'),
                'availabilityAbsences' => fn (EloquentBuilder $query) => $query
                    ->where('start_date', '<=', $end)
                    ->where('end_date', '>=', $start),
                'availabilityExceptions' => fn (EloquentBuilder $query) => $query
                    ->where('date', '>=', $start)
                    ->where('date', '<=', $end)
                    ->whereNotNull('room_id')
                    ->whereNotNull('start'),
            ])
            ->whereIn('planable_type', $types)
            //->where('is_hidden', '=', false)
            ->where('start_date', '<=', $end)
            ->where(fn (Builder $query) => $query
                ->where('end_date', '>=', $start)
                ->orWhereNull('end_date'))
            ->get()
            ->map(fn (Availability $record) => $record->getEventsBetween($start, $end))
            ->flatten(1);

        return Cache::supportsTags() ? Cache::tags(['availability', 'events'])->flexible(
            key: sprintf("availability-events-%s-%s", $start->toIso8601String(), $end->toIso8601String()),
            ttl: [60, 80],
            callback: $callback) : $callback();
    }

    private function getGroupedUserAvailabilityEvents(CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        if(!calendar()->group_users_in_day_plan) {
            return collect();
        }
        return Availability::query()
            ->with([
                'availabilityShifts' => fn (EloquentBuilder $query) => $query
                    ->whereNotNull('room_id')
                    ->whereNotNull('start'),
                'availabilityAbsences' => fn (EloquentBuilder $query) => $query
                    ->where('start_date', '<=', $end)
                    ->where('end_date', '>=', $start),
                'availabilityExceptions' => fn (EloquentBuilder $query) => $query
                    ->where('date', '>=', $start)
                    ->where('date', '<=', $end)
                    ->whereNotNull('room_id')
                    ->whereNotNull('start'),
            ])
            ->where('planable_type', User::class)
            //->where('is_hidden', '=', false)
            ->where('start_date', '<=', $end)
            ->where(fn (Builder $query) => $query
                ->where('end_date', '>=', $start)
                ->orWhereNull('end_date'))
            ->get()
            ->map(fn (Availability $record) => $record->getRecordsBetween($start, $end))
            ->flatten(1)
            ->groupBy(['room.id', fn (array $event) => $event['date']->format('Y-m-d')])
                ->map(fn (Collection $group, $room) => $group->map(fn (Collection $events, $date) => [
                'id' => sprintf("resource-group-%s-%s", $room, $date),
                'title' => sprintf("%s (%s)", __('Users'), $events->count()),
                'start' => Carbon::parse($date),
                'end' => Carbon::parse($date)->endOfDay(),
                'resourceId' => $room,
                'color' => calendar()->user_group_color,
                'allDay' => true,
                'model' => User::class,
                'type' => 'availability-group',
                'room' => $room,
            ]))->flatten(1);
    }

    private function getGroupedResourceAvailabilityEvents(CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        if(!calendar()->group_resources_in_day_plan) {
            return collect();
        }
        return Availability::query()
            ->with([
                'availabilityShifts' => fn (EloquentBuilder $query) => $query
                    ->whereNotNull('room_id')
                    ->whereNotNull('start'),
                'availabilityAbsences' => fn (EloquentBuilder $query) => $query
                    ->where('start_date', '<=', $end)
                    ->where('end_date', '>=', $start),
                'availabilityExceptions' => fn (EloquentBuilder $query) => $query
                    ->where('date', '>=', $start)
                    ->where('date', '<=', $end)
                    ->whereNotNull('room_id')
                    ->whereNotNull('start'),
            ])
            ->where('planable_type', SystemResource::class)
            //->where('is_hidden', '=', false)
            ->where('start_date', '<=', $end)
            ->where(fn (Builder $query) => $query
                ->where('end_date', '>=', $start)
                ->orWhereNull('end_date'))
            ->get()
            ->map(fn (Availability $record) => $record->getRecordsBetween($start, $end))
            ->flatten(1)
            ->groupBy(['room.id', fn (array $event) => $event['date']->format('Y-m-d')])
                ->map(fn (Collection $group, $room) => $group->map(fn (Collection $events, $date) => [
                'id' => sprintf("resource-group-%s-%s", $room, $date),
                'title' => sprintf("%s (%s)", __('Resources'), $events->count()),
                'start' => Carbon::parse($date),
                'end' => Carbon::parse($date)->endOfDay(),
                'resourceId' => $room,
                'color' => calendar()->resource_group_color,
                'allDay' => true,
                'model' => SystemResource::class,
                'type' => 'availability-group',
                'room' => $room,
            ]))->flatten(1);
    }

    public function onAvailabilityClick(array $event, ?string $modifier = null): void
    {
        $start = Carbon::parse($event['start']);
        $this->record = Availability::with([
            'availabilityExceptions' => fn (EloquentBuilder $query) => $query->where('date', $start),
            'availabilityAbsences' => fn (EloquentBuilder $query) => $query
                ->where('start_date', '<=', $start)
                ->where('end_date', '>=', $start),
        ])->findOrFail($event['id']);

        $this->mountAction('editAvailability', [
            'type' => 'click',
            'event' => $event,
        ]);
    }

    public function onAvailabilityGroupClick(array $event, ?string $modifier = null): void
    {
        $this->mountAction('viewAvailabilityGroup', [
            'type' => 'click',
            'event' => $event,
        ]);
    }

    protected function createAvailabilityAction(): Action
    {
        return CreateAction::make('createAvailability')
            ->model(AvailabilityException::class)
            ->fillForm(function (array $arguments) {
                $data = [
                    'date' => $arguments['start'],
                    'room_id' => $arguments['resource']['id'] ?? null,
                ];
                return $data;
            })
            ->schema([
                DatePicker::make('date')
                    ->required()
                    ->readOnly(),
                Select::make('room_id')
                    ->relationship('room', 'name')
                    ->required(),
                Select::make('availability_id')
                    ->label(__('Availability'))
                    ->relationship(
                        name: 'availability',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('start_date', '<=', $get('date'))
                            ->where(function (Builder $query) use ($get) {
                                $query->whereNull('end_date')
                                    ->orWhere('end_date', '>=', $get('date'));
                            })->where('is_hidden', false),
                    )
                    ->preload()
                    ->required()
                    ->searchable()
                    ->reactive(),
                TimePicker::make('start')
                    ->required(),
                TimePicker::make('target_minutes')
                    ->required()
                    ->formatStateUsing(fn ($state) => formatTime($state))
                    ->dehydrateStateUsing(fn ($state) => deformatTime($state)),
            ]);
    }

    protected function editAvailabilityAction(): Action
    {
        return EditAction::make('editAvailability')
            ->model(Availability::class)
            ->modal()
            ->modalHeading(__('filament-actions::edit.single.modal.heading', ['label' => __('Availability')]))
            ->modalContentFooter(function (array $arguments, Availability $record) {
                $date = Carbon::parse($arguments['event']['start']);
                $records = $record->getRecordsBetween($date->toImmutable(), $date->addDays(10)->toImmutable());
                return view('forms.components.availabilities.event-overview', ['records' => $records]);
            })
            /*->registerModalActions([
                Action::make('createAbsenceForDate')
                    ->requiresConfirmation()
                    ->action(fn (Availability $record) => $record->report()),
            ])*/
            ->fillForm(function (array $arguments, Availability $record) {
                $data = $record->attributesToArray();
                $data['date'] = $arguments['event']['start'];
                return $data;
            })
            ->schema([
                DatePicker::make('date')
                    ->disabled(),
                TableRepeater::make('availabilityExceptions')
                    ->relationship('availabilityExceptions')
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data, Get $get) {
                        $data['date'] = $get('date');
                        return $data;
                    })
                    ->columnSpanFull()
                    ->headers([
                        Header::make('room_id'),
                        Header::make('start')
                            ->markAsRequired(),
                        Header::make('target_minutes')
                            ->markAsRequired(),
                    ])
                    ->schema([
                        Select::make('room_id')
                            ->relationship('room', 'name'),
                        TimePicker::make('start')
                            ->required(),
                        TimePicker::make('target_minutes')
                            ->required()
                            ->formatStateUsing(fn ($state) => formatTime($state, '%H:%I'))
                            ->dehydrateStateUsing(fn ($state) => deformatTime($state)),
                    ]),
                TableRepeater::make('availabilityAbsences')
                    ->relationship('availabilityAbsences')
                    ->columnSpanFull()
                    ->addAction(fn (Action $action, Get $get) => $action
                        ->action(function (Repeater $component) use ($get): void  {
                            $newUuid = $component->generateUuid();

                            $items = $component->getState();

                            if ($newUuid) {
                                $items[$newUuid] = [];
                            } else {
                                $items[] = [];
                            }

                            $component->state($items);

                            $component->getChildComponentContainer($newUuid ?? array_key_last($items))->fill([
                                'start_date' => $get('date'),
                                'end_date' => $get('date'),
                            ]);

                            $component->collapsed(false, shouldMakeComponentCollapsible: false);

                            $component->callAfterStateUpdated();
                        }))
                    ->headers([
                        Header::make('start')
                            ->markAsRequired(),
                        Header::make('end')
                            ->markAsRequired(),
                    ])
                    ->schema([
                        DatePicker::make('start_date')
                            ->required(),
                        DatePicker::make('end_date')
                            ->required(),
                    ]),
            ]);

    }

    protected function viewAvailabilityGroupAction(): Action
    {
        return Action::make('viewAvailabilityGroup')
            ->schema(function (array $arguments) {
                $date = CarbonImmutable::parse($arguments['event']['start'])->startOfDay();
                $events = Availability::query()
                    ->with([
                        'availabilityShifts' => fn (EloquentBuilder $query) => $query
                            ->where('room_id', $arguments['event']['extendedProps']['room'])
                            ->whereNotNull('start'),
                        'availabilityAbsences' => fn (EloquentBuilder $query) => $query
                            ->where('start_date', '<=', $date)
                            ->where('end_date', '>=', $date),
                        'availabilityExceptions' => fn (EloquentBuilder $query) => $query
                            ->where('date', '>=', $date)
                            ->where('date', '<=', $date)
                            ->where('room_id', $arguments['event']['extendedProps']['room'])
                            ->whereNotNull('start'),
                    ])
                    ->where('planable_type', $arguments['event']['extendedProps']['model'])
                    //->where('is_hidden', '=', false)
                    ->where('start_date', '<=', $date)
                    ->where(fn (Builder $query) => $query
                        ->where('end_date', '>=', $date)
                        ->orWhereNull('end_date'))
                    ->get()
                    ->map(fn (Availability $record) => $record->getRecordsForDate($date))
                    ->flatten(1);
                return [
                    RepeatableEntry::make('resource')
                        ->state(fn () => $events->map(fn (AvailabilityEvent $event) => [
                            'name' => $event->availability->getPlanableTitle(),
                            'start' => $event->getStartTime(),
                            'end' => $event->getEndTime(),
                            'record' => $event->availability->id,
                        ]))
                        ->columns(3)
                        ->schema([
                            TextEntry::make('record')->hidden(),
                            TextEntry::make('name'),
                            TextEntry::make('start')
                                ->time(getTimeFormat()),
                            TextEntry::make('end')
                                ->time(getTimeFormat())
                                ->suffixAction($this->editAvailabilityAction()
                                    ->icon(Heroicon::PencilSquare)
                                    ->record(fn (Get $get) => Availability::find($get('record')))
                                    ->modalContentFooter(function (Availability $record) use($date) {
                                        $records = $record->getRecordsBetween($date->toImmutable(), $date->addDays(10)->toImmutable());
                                        return view('forms.components.availabilities.event-overview', ['records' => $records]);
                                    })
                                    ->fillForm(function (Availability $record) {
                                        $data = $record->attributesToArray();
                                        return $data;
                                    })
                                ),
                        ]),
                ];
            });
    }
}
