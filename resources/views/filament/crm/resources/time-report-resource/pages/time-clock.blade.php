<x-filament-panels::page>
    <x-filament::section class="flex justify-center">
        <form wire:submit="check" class="w-80 flex flex-col justify-center gap-6">
            <div class="">
                {{ $this->form }}
            </div>

            <x-filament::button type="submit">
                {{__('Check')}}
            </x-filament::button>
        </form>
    </x-filament::section>
</x-filament-panels::page>
