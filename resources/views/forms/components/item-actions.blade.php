@php
    use Filament\Actions\Action;

    $containers = $getItems();
    $extraItemActions = $getExtraItemActions();
    $extraActions = $getExtraActions();

@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{}" {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class(['grid gap-y-4'])
    }}>
        <ul class="flex flex-wrap gap-x-3 gap-y-2">
            @if (count($containers))
                @foreach ($containers as $uuid => $item)
                    @php
                        $visibleExtraItemActions = array_filter(
                            $extraItemActions,
                            fn (Action $action): bool => $action(['item' => $uuid])->isVisible(),
                        );
                    @endphp

                    <li wire:key="{{ $this->getId() }}.{{ $item->getStatePath() }}.{{ $field::class }}.item">
                        <ul
                                class="ms-auto flex gap-x-3"
                        >
                            @foreach ($visibleExtraItemActions as $extraItemAction)
                                <li x-on:click.stop>
                                    {{ $extraItemAction(['item' => $uuid]) }}
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            @endif
            @php
                $visibleExtraActions = array_filter(
                    $extraActions,
                    fn (Action $action): bool => $action(['item' => $uuid])->isVisible(),
                );
            @endphp
            <li wire:key="{{ $this->getId() }}.{{ $field::class }}.extraActions">
                <ul class="ms-auto flex gap-x-3">
                    @foreach ($visibleExtraActions as $extraAction)
                        <li x-on:click.stop>
                            {{ $extraAction(['item' => $uuid]) }}
                        </li>
                    @endforeach
                </ul>
            </li>
        </ul>
    </div>
</x-dynamic-component>
