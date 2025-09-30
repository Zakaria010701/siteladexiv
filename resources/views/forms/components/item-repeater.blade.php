@php
    use Filament\Actions\Action;

    $containers = $getChildComponentContainers();

    $addAction = $getAction($getAddActionName());
    $addBetweenAction = $getAction($getAddBetweenActionName());
    $cloneAction = $getAction($getCloneActionName());
    $collapseAllAction = $getAction($getCollapseAllActionName());
    $expandAllAction = $getAction($getExpandAllActionName());
    $deleteAction = $getAction($getDeleteActionName());
    $moveDownAction = $getAction($getMoveDownActionName());
    $moveUpAction = $getAction($getMoveUpActionName());
    $reorderAction = $getAction($getReorderActionName());
    $extraItemActions = $getExtraItemActions();

    $isAddable = $isAddable();
    $isCloneable = $isCloneable();
    $isCollapsible = $isCollapsible();
    $isDeletable = $isDeletable();
    $isReorderableWithButtons = $isReorderableWithButtons();
    $isReorderableWithDragAndDrop = $isReorderableWithDragAndDrop();

    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{}"
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class(['fi-fo-repeater grid gap-y-4'])
        }}
    >
        @if ($isCollapsible && ($collapseAllAction->isVisible() || $expandAllAction->isVisible()))
            <div
                @class([
                    'flex gap-x-3',
                    'hidden' => count($containers) < 2,
                ])
            >
                @if ($collapseAllAction->isVisible())
                    <span
                        x-on:click="$dispatch('repeater-collapse', '{{ $statePath }}')"
                    >
                        {{ $collapseAllAction }}
                    </span>
                @endif

                @if ($expandAllAction->isVisible())
                    <span
                        x-on:click="$dispatch('repeater-expand', '{{ $statePath }}')"
                    >
                        {{ $expandAllAction }}
                    </span>
                @endif
            </div>
        @endif

        @if (count($containers))
            <ul>
                <div class="grid-cols-2">
                    @foreach ($containers as $uuid => $item)
                        @php
                            $itemLabel = $getItemLabel($uuid);
                            $badge = $getItemBadge($uuid);
                            $color = $getItemColor($uuid);
                            $textColor = $getItemTextColor($uuid);
                            $filled = $getItemFilled($uuid);
                            $visibleExtraItemActions = array_filter(
                                $extraItemActions,
                                fn (Action $action): bool => $action(['item' => $uuid])->isVisible(),
                            );
                        @endphp

                        <li
                            wire:key="{{ $this->getId() }}.{{ $item->getStatePath() }}.{{ $field::class }}.item"
                            x-data="{
                                isCollapsed: @js($isCollapsed($item)),
                            }"
                            x-on:expand="isCollapsed = false"
                            x-on:repeater-expand.window="$event.detail === '{{ $statePath }}' && (isCollapsed = false)"
                            x-on:repeater-collapse.window="$event.detail === '{{ $statePath }}' && (isCollapsed = true)"
                            x-sortable-item="{{ $uuid }}"
                            style="{{\Filament\Support\get_color_css_variables(
                                $color,
                                shades: [100, 400, 500, 600],
                                alias: 'button',
                            )}}"
                            @class([
                                "fi-fo-repeater-item rounded-xl shadow-sm ring-1 ring-gray-950/5",
                                "border-2 border-custom-600" => isset($color),
                                'bg-custom-600' => isset($color) && $filled,
                                'bg-custom-100' => isset($color) && !$filled,
                            ])
                            x-bind:class="{ 'fi-collapsed overflow-hidden': isCollapsed }"
                        >
                            @if ($isReorderableWithDragAndDrop || $isReorderableWithButtons || filled($itemLabel) || $isCloneable || $isDeletable || $isCollapsible || count($visibleExtraItemActions))
                                <div
                                    @if ($isCollapsible)
                                        x-on:click.stop="isCollapsed = !isCollapsed"
                                    @endif

                                    @class([
                                        'fi-fo-repeater-item-header px-2 py-2 overflow-hidden',
                                        'cursor-pointer select-none' => $isCollapsible,
                                    ])
                                >
                                    <div class="flex items-center gap-x-3">
                                        @if ($isReorderableWithDragAndDrop || $isReorderableWithButtons)
                                            <ul class="flex items-center gap-x-3">
                                                @if ($isReorderableWithDragAndDrop)
                                                    <li
                                                        x-sortable-handle
                                                        x-on:click.stop
                                                    >
                                                        {{ $reorderAction }}
                                                    </li>
                                                @endif

                                                @if ($isReorderableWithButtons)
                                                    <li
                                                        x-on:click.stop
                                                        class="flex items-center justify-center"
                                                    >
                                                        {{ $moveUpAction(['item' => $uuid])->disabled($loop->first) }}
                                                    </li>

                                                    <li
                                                        x-on:click.stop
                                                        class="flex items-center justify-center"
                                                    >
                                                        {{ $moveDownAction(['item' => $uuid])->disabled($loop->last) }}
                                                    </li>
                                                @endif
                                            </ul>
                                        @endif

                                        @if (filled($itemLabel))
                                            <p
                                                style="{{\Filament\Support\get_color_css_variables(
                                                    $textColor,
                                                    shades: [500],
                                                    alias: 'button',
                                                )}}"
                                                @class([
                                                    'text-sm font-medium break-words',
                                                    'text-custom-500' => isset($textColor),
                                                    'text-gray-950' => !isset($textColor),
                                                    'truncate' => $isItemLabelTruncated(),
                                                ])
                                            >
                                                {{ $itemLabel }}

                                            </p>
                                        @endif



                                        @if ($isCloneable || $isDeletable || $isCollapsible || count($visibleExtraItemActions))
                                            <ul
                                                class="ms-auto flex gap-x-3"
                                            >
                                                @if (filled($badge) && $badge != false)
                                                <li>
                                                    <span
                                                        style="{{\Filament\Support\get_color_css_variables(
                                                            $textColor,
                                                            shades: [600],
                                                            alias: 'button',
                                                        )}}"
                                                        @class([
                                                            'flex items-center gap-x-3 rounded-md ring-inset px-2',
                                                            "bg-custom-600" => isset($color),
                                                        ])>
                                                        <span style="{{\Filament\Support\get_color_css_variables(
                                                            $color,
                                                            shades: [500],
                                                            alias: 'button',
                                                        )}}" @class([
                                                            'text-custom-500' => isset($textColor),
                                                            'text-gray-950' => !isset($textColor),
                                                        ])>5</span>
                                                    </span>
                                                </li>
                                                @endif
                                                @foreach ($visibleExtraItemActions as $extraItemAction)
                                                    <li x-on:click.stop>
                                                        {{ $extraItemAction(['item' => $uuid]) }}
                                                    </li>
                                                @endforeach

                                                @if ($isCloneable)
                                                    <li x-on:click.stop>
                                                        {{ $cloneAction(['item' => $uuid]) }}
                                                    </li>
                                                @endif

                                                @if ($isDeletable)
                                                    <li x-on:click.stop>
                                                        {{ $deleteAction(['item' => $uuid]) }}
                                                    </li>
                                                @endif

                                                @if ($isCollapsible)
                                                    <li
                                                        class="relative transition"
                                                        x-on:click.stop="isCollapsed = !isCollapsed"
                                                        x-bind:class="{ '-rotate-180': isCollapsed }"
                                                    >
                                                        <div
                                                            class="transition"
                                                            x-bind:class="{ 'opacity-0 pointer-events-none': isCollapsed }"
                                                        >
                                                            {{ $getAction('collapse') }}
                                                        </div>

                                                        <div
                                                            class="absolute inset-0 rotate-180 transition"
                                                            x-bind:class="{ 'opacity-0 pointer-events-none': ! isCollapsed }"
                                                        >
                                                            {{ $getAction('expand') }}
                                                        </div>
                                                    </li>
                                                @endif
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div
                                x-show="! isCollapsed"
                                class="fi-fo-repeater-item-content border-t border-gray-100 p-4"
                            >
                                {{ $item }}
                            </div>
                        </li>

                        @if (! $loop->last)
                            @if ($isAddable && $addBetweenAction->isVisible())
                                <li class="flex w-full justify-center">
                                    <div
                                        class="fi-fo-repeater-add-between-action-ctn rounded-lg bg-white"
                                    >
                                        {{ $addBetweenAction(['afterItem' => $uuid]) }}
                                    </div>
                                </li>
                            @elseif (filled($labelBetweenItems = $getLabelBetweenItems()))
                                <li
                                    class="relative border-t border-gray-200"
                                >
                                    <span
                                        class="absolute -top-3 left-3 px-1 text-sm font-medium"
                                    >
                                        {{ $labelBetweenItems }}
                                    </span>
                                </li>
                            @endif
                        @endif
                    @endforeach
                </div>
            </ul>
        @endif

        @if ($isAddable)
            <div class="flex justify-center">
                {{ $addAction }}
            </div>
        @endif
    </div>
</x-dynamic-component>
