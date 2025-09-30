<div class="cms-block py-8">
    @if(isset($content['title']) && $content['title'])
        <h2 class="text-3xl font-bold mb-6">{{ $content['title'] }}</h2>
    @endif
    <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-300">
            @if(isset($content['columns']) && count($content['columns']) > 0)
                <thead>
                    <tr class="bg-gray-100">
                        @foreach($content['columns'] as $column)
                            <th class="border border-gray-300 px-4 py-2 text-left font-semibold">{{ $column['header'] ?? '' }}</th>
                        @endforeach
                    </tr>
                </thead>
            @endif
            <tbody>
                @if(isset($content['rows']) && count($content['rows']) > 0)
                    @foreach($content['rows'] as $row)
                        <tr class="hover:bg-gray-50">
                            @if(isset($row['cells']) && count($row['cells']) > 0)
                                @foreach($row['cells'] as $cell)
                                    <td class="border border-gray-300 px-4 py-2">{{ $cell['content'] ?? '' }}</td>
                                @endforeach
                            @endif
                            {{-- Pad if fewer cells than columns --}}
                            @for($i = count($row['cells'] ?? []); $i < count($content['columns'] ?? []); $i++)
                                <td class="border border-gray-300 px-4 py-2"></td>
                            @endfor
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>