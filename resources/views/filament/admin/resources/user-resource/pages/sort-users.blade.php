<x-filament-panels::page>

    <!-- Hier ist der Inhalt der Seite, den du bereits hast -->

    <x-filament-panels::form>
    
        <!-- Hier ist der Formularinhalt, den du bereits hast -->
    
        {{$this->form}}
    
    </x-filament-panels::form>

    <!-- Hier fügst du den Button hinzu -->
    <div class="actions-container">
        <a href="{{ route('sort-users') }}" class="btn btn-primary">Zurück zur Sortierseite</a>
    </div>
    <x-filament::button wire:click="openNewUserModal">
    New user
</x-filament::button>

</x-filament-panels::page>
