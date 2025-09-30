<div>
    <table class="text-sm w-full table-auto">
        <thead>
        <tr>
            <th class="text-left">{{__('Date')}}</th>
            <th class="text-left">{{__('Type')}}</th>
            <th class="text-left">{{__('Category')}}</th>
            <th class="text-left">{{__('Services')}}</th>
        </tr>
        </thead>
            <tr>
                <td class="text-sm">{{ formatDate($appointment->start) }}</td>
                <td class="text-sm">{{ $appointment->type->getLabel() }}</td>
                <td class="text-sm">
                    {{ $appointment->category->name }}
                </td>
                <td class="flex gap-1.5 flex-wrap">
                    @foreach($appointment->getServices() as $service)
                        @php
                            $color = in_array($service->id, $services) ? 'success' : 'danger';
                        @endphp
                        <x-filament::badge color="{{$color}}">{{ $service->name }}</x-filament::badge>
                    @endforeach
                </td>
            </tr>
    </table>
</div>