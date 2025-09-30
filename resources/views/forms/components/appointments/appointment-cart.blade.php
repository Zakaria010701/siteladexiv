<div class="text-sm w-full">
    @foreach($items as $item)
        <div class="flex flex-row justify-between">
            <div class="grow">
                {{ $item['description'] }}
            </div>
            <div class="text-right">
                {{ formatMoney($item['sub_total']) }}
            </div>
        </div>
    @endforeach
</div>
