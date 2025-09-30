<div class="actions-container">
    <a href="{{ route('sort-users') }}" class="btn btn-primary">Benutzerdefinierte Seite</a>
</div>
<!-- Inhalt der actions.blade.php -->

@include('filament.admin.resources.user-resource.pages.custom_action_button')

<x-filament-panels::button
    :href="route('edit', $record)"
    class="btn btn-primary" <!-- Hier fügst du deine benutzerdefinierte CSS-Klasse hinzu -->
>
    Bearbeiten
</x-filament-panels::button>

