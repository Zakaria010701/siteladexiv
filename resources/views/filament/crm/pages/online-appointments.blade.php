<x-filament-panels::page>
    <div class="flex flex-col gap-y-6">

        {{--<x-filament-panels::resources.tabs />--}}

        {{ $this->table }}

         @livewire(\App\Filament\Crm\Widgets\WaitingListWidget::class)

    </div>
</x-filament-panels::page>
