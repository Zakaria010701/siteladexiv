<div>
    <div class="flex flex-col gap-6">
        {{$this->appointmentInfoList}}

        <form wire:submit="save">
            {{ $this->form }}
        </form>
    </div>

    <x-filament-actions::modals />
</div>
