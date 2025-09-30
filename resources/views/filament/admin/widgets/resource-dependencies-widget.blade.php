<x-filament-widgets::widget>
    <div class="relative overflow-x-auto divide-y divide-gray-200 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
        <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
            <thead class="divide-y divide-gray-200 dark:divide-white/5">
                <tr>
                    <th class="whitespace-nowrap">
                        <div class="grid w-full gap-y-1 px-3 py-3 text-left">
                            {{ __('Resource')  }}
                        </div>
                    </th>
                    @foreach($this->records as $record)
                        <th class="whitespace-nowrap">
                            <div class="grid w-full gap-y-1 px-3 py-3">
                                <a href="{{ $this->getRecordUrl($record) }}">
                                    {{ $record->{$this->titleAttribute}  }}
                                </a>
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            @foreach($this->relationships as $relationship)
            <tbody x-data="{expanded: false}" class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                     <tr>
                         <td class="p-0 bg-gray-50" colspan="{{$this->record_count+1}}">
                             <div x-on:click="expanded = !expanded" class="w-full text-left">
                                 <div class="w-full flex flex-row gap-y-1 px-3 py-2">
                                     <div>{{ $relationship['label'] }}</div>
                                     <x-filament::icon-button
                                         icon="heroicon-m-chevron-down"
                                         color="gray"
                                         x-show="!expanded"
                                     />
                                     <x-filament::icon-button
                                         icon="heroicon-m-chevron-up"
                                         color="gray"
                                         x-show="expanded"
                                     />
                                 </div>
                             </div>
                         </td>
                     </tr>
                    @foreach($relationship['records'] as $related)
                        <tr x-show="expanded">
                            <td class="p-0">
                                <a href="{{ $relationship['resource']::getUrl($relationship['route'], ['record' => $related]) }}">
                                    <div class="grid w-full gap-y-1 px-3 py-4">
                                        {{ $related->{$relationship['titleAttribute']} }}
                                    </div>
                                </a>
                            </td>
                            @foreach($this->records as $record)
                                <td class="p-0">
                                    <div class="grid w-full gap-y-1 px-3 py-4 flex items-center">
                                        @if(in_array($related->id, $relationship['relatedIds'][$record->id]))
                                            <x-filament::icon
                                                icon="heroicon-m-check-circle"
                                                class="h-5 w-5"
                                                style="color: #16A34A"
                                            />
                                        @else
                                            <x-filament::icon
                                                icon="heroicon-m-x-circle"
                                                class="h-5 w-5"
                                                style="color: #DC2626"
                                            />
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
            </tbody>
            @endforeach
        </table>
    </div>
</x-filament-widgets::widget>
