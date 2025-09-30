<x-filament::dropdown teleport placement="bottom-end">
    <x-slot name="trigger">
        <button type="button"
                class="flex items-center justify-center w-full p-2 text-sm font-medium rounded-lg shadow-sm outline-none group gap-x-3 bg-primary-500">
                <span class="w-5 h-5 font-semibold bg-white rounded-full shrink-0 text-indigo-500">
                    {{str($label)->substr(0, 1)->upper()}}
                </span>
                <span class="text-white">
                    {{ $label }}
                </span>

            <x-filament::icon
                icon="heroicon-m-chevron-down"
                icon-alias="panels::panel-switch-simple-icon"
                class="w-5 h-5 text-white ms-auto shrink-0"
            />

        </button>
    </x-slot>

    <x-filament::dropdown.list>
        @foreach ($options as $key => $option)
            <x-filament::dropdown.list.item
                :badge="str($option)->substr(0, 2)->upper()"
                wire:click="switch({{$key}}, '{{$option}}')"
                value="{{$key}}"
                tag="a"
            >
                {{ $option }}
            </x-filament::dropdown.list.item>
        @endforeach
    </x-filament::dropdown.list>

</x-filament::dropdown>
