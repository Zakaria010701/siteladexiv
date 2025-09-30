<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        <x-filament::section>
            {{ $this->form }}
        </x-filament::section>


        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>


    <x-filament-panels::page.unsaved-data-changes-alert />
</x-filament-panels::page>
