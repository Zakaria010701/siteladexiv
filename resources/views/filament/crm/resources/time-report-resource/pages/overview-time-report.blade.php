<x-filament-panels::page>
    <x-filament::section>
        <form wire:submit="showReport">
            <div class="">
            {{ $this->form }}
            </div>

            <x-filament::button type="submit">
                {{__('Show')}}
            </x-filament::button>
        </form>
    </x-filament::section>
    <x-filament::section>
        <table class="table-auto w-full text-sm text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-4 py-3">{{__('Date')}}</th>
                    <th scope="col" class="px-4 py-3">{{__('Target')}}</th>
                    <th scope="col" class="px-4 py-3">{{__('Start')}}</th>
                    <th scope="col" class="px-4 py-3">{{__('End')}}</th>
                    <th scope="col" class="px-4 py-3">{{__('Total')}}</th>
                    <th scope="col" class="px-4 py-3">{{__('Break')}}</th>
                    <th scope="col" class="px-4 py-3">{{__('Leave')}}</th>
                    <th scope="col" class="px-4 py-3">{{__('Manual')}}</th>
                    <th scope="col" class="px-4 py-3">{{__('Actual')}}</th>
                    <th scope="col" class="px-4 py-3">{{__('Overtime')}}</th>
                    <th scope="col" class="px-4 py-3">{{__('Note')}}</th>
                    <th scope="col" class="px-4 py-3">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" @class([
                            'px-4',
                            'py-2',
                            'font-medium',
                            'text-gray-900' => is_null($record->payroll),
                            'text-primary-500' => !is_null($record->payroll),
                            'whitespace-nowrap',
                            'dark:text-white' => is_null($record->payroll),
                            'dark:text-primary-500' => !is_null($record->payroll),
                            'text-left',
                            'content-start',
                        ])>
                            {{ formatDate($record->date) }}
                        </th>
                        <td class="px-4 py-2 content-start">{{ formatTime($record->target_minutes) }}</td>
                        <td style="{{\Filament\Support\get_color_css_variables(
                                $record->time_in_status?->getColor() ?? 'gray',
                                shades: [500],
                                alias: 'time_in_status',
                            )}}"
                            class="px-4 py-2 content-start"
                        >
                            <span class="text-custom-500">{{ $record->time_in?->format('H:i') ?? '--:--' }}</span>
                            @if($record->isEdited())<br><span class="text-gray-300">{{ $record->real_time_in?->format('H:i') ?? '--:--' }}</span>@endif
                            @isset($record->work_time_start)<br><span class="text-blue-500">{{ $record->work_time_start->format('H:i') }}</span>@endisset
                        </td>
                        <td style="{{\Filament\Support\get_color_css_variables(
                                $record->time_out_status?->getColor() ?? 'gray',
                                shades: [500],
                                alias: 'time_out_status',
                            )}}"
                            class="px-4 py-2 content-start"
                        >
                            <span class="text-custom-500">{{ $record->time_out?->format('H:i') ?? '--:--' }}</span>
                            @if($record->isEdited())<br><span class="text-gray-300">{{ $record->real_time_out?->format('H:i') ?? '--:--' }}</span>@endif
                            @isset($record->work_time_end)<br><span class="text-blue-500">{{ $record->work_time_end->format('H:i') }}</span>@endisset
                        </td>
                        <td class="px-4 py-2 content-start">{{ formatTime($record->total_minutes) }}</td>
                        <td class="px-4 py-2 content-start">{{ formatTime($record->break_minutes) }}</td>
                        <td class="px-4 py-2 content-start">
                            @isset($record->leave_type)
                            <x-filament::badge size="sm" :color="$record->leave_type->getColor()">
                                {{$record->leave_type->getLabel()}}
                            </x-filament::badge>
                            @endisset
                        </td>
                        <td class="px-4 py-2 content-start">{{ formatTime($record->manual_minutes) }}</td>
                        <td class="px-4 py-2 content-start">{{ formatTime($record->actual_minutes) }}</td>
                        <td class="px-4 py-2 content-start" style="{{\Filament\Support\get_color_css_variables(
                                (($record->overtime_minutes > 0) ? 'success' : (($record->overtime_minutes < 0) ? 'danger' : 'gray')),
                                shades: [500],
                                alias: 'time_out_status',
                            )}}">
                            <span class="text-custom-500">{{ formatTime($record->overtime_minutes) }}</span>
                            @if($record->overtime_minutes != $record->uncapped_overtime_minutes)
                                <br><span class="text-gray-300">{{ formatTime($record->uncapped_overtime_minutes) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 content-start">{{ $record->note }}</td>
                        <td class="px-4 py-2 content-start flex flex-row">

                            @if(($this->controlAction)(['report' => $record->id])->isVisible())
                                {{ ($this->controlAction)(['report' => $record->id]) }}
                            @endif
                            @if(($this->editAction)(['report' => $record->id])->isVisible())
                                {{ ($this->editAction)(['report' => $record->id]) }}
                            @endif
                            <x-filament-actions::group :actions="[
                                ($this->undoAction)(['report' => $record->id]),
                                {{--($this->payrollAction)(['report' => $record->id]),--}}
                                {{--($this->deletePayrollAction)(['report' => $record->id]),--}}
                            ]" />

                            <x-filament-actions::modals />
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-4 py-2 content-start font-medium text-gray-900 whitespace-nowrap dark:text-white text-left">{{__('Total')}}</th>
                    <td class="px-4 py-2 content-start" colspan="3">{{ formatTime($overview->target_minutes) }}</td>
                    <td class="px-4 py-2 content-start" colspan="2">{{ formatTime($overview->total_minutes) }}</td>
                    <td class="px-4 py-2 content-start" colspan="2">{{ $overview->vacation_days }}</td>
                    <td class="px-4 py-2 content-start">{{ formatTime($overview->actual_minutes) }}</td>
                    <td class="px-4 py-2 content-start" colspan="3">{{ formatTime($overview->overtime_minutes) }}</td>
                </tr>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-4 py-2 content-start text-right font-medium text-gray-900" colspan="6">{{__('Vacation Carry')}}</td>
                    <td class="px-4 py-2 content-start" colspan="2">{{ $overview->carry_vacation_days }}</td>
                    <td class="px-4 py-2 content-start text-right font-medium text-gray-900">{{__('Overtime Carry')}}</td>
                    <td class="px-4 py-2 content-start" colspan="3">{{ formatTime($overview->carry_overtime_minutes) }}</td>
                </tr>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-4 py-2 content-start text-right font-medium text-gray-900" colspan="6">{{__('Vacation Total')}}</td>
                    <td class="px-4 py-2 content-start" colspan="2">{{ $overview->total_vacation }}</td>
                    <td class="px-4 py-2 content-start text-right font-medium text-gray-900">{{__('Overtime Total')}}</td>
                    <td class="px-4 py-2 content-start" colspan="3">{{ formatTime($overview->total_overtime) }}</td>
                </tr>
            </tfoot>
        </table>
    </x-filament::section>
</x-filament-panels::page>
