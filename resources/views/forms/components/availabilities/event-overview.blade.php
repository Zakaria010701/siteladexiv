<div>
    <table class="w-full">
        <thead class="rounded-t-xl overflow-hidden border-b border-gray-950/5 dark:border-white/20">
            <tr class="text-xs md:divide-x md:divide-gray-950/5 dark:md:divide-white/20">
                <th class="p-2 font-medium first:rounded-tl-xl last:rounded-tr-xl bg-gray-100 dark:text-gray-300 dark:bg-gray-900/60 text-start">
                    {{ __('Date') }}
                </th>
                <th class="p-2 font-medium first:rounded-tl-xl last:rounded-tr-xl bg-gray-100 dark:text-gray-300 dark:bg-gray-900/60 text-start">
                    {{ __('Start') }}
                </th>
                <th class="p-2 font-medium first:rounded-tl-xl last:rounded-tr-xl bg-gray-100 dark:text-gray-300 dark:bg-gray-900/60 text-start">
                    {{ __('End') }}
                </th>
                <th class="p-2 font-medium first:rounded-tl-xl last:rounded-tr-xl bg-gray-100 dark:text-gray-300 dark:bg-gray-900/60 text-start">
                    {{ __('Room') }}
                </th>
                <th class="p-2 font-medium first:rounded-tl-xl last:rounded-tr-xl bg-gray-100 dark:text-gray-300 dark:bg-gray-900/60 text-start"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-950/5 dark:divide-white/20">
            @foreach ($records as $record)
                <tr>
                    <td class="align-top p-2 text-start">{{ formatDate($record['date']) }}</td>
                    <td class="align-top p-2 text-start">{{ $record['start']->format('H:i') }}</td>
                    <td class="align-top p-2 text-start">{{ $record['end']->format('H:i') }}</td>
                    <td class="align-top p-2 text-start">{{ $record['room']?->name }}</td>
                    <td>
                        
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
