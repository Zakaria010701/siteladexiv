<?php

namespace App\Forms\Components;

use Filament\Schemas\Components\Contracts\HasExtraItemActions;
use Filament\Support\Enums\Size;
use Filament\Actions\Action;
use Filament\Forms\Components\Concerns;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Concerns\HasChildComponents;
use Illuminate\Support\Arr;

class ItemActions extends Field implements HasExtraItemActions
{
    use Concerns\HasExtraItemActions;

    protected string $view = 'forms.components.item-actions';

    protected ?string $relatedModel = null;

    /**
     * @var array<Action|Closure>
     */
    protected array $extraActions = [];

    /**
     * @var array<Action>|null
     */
    protected ?array $cachedExtraActions = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);

        $this->registerActions([
            fn (ItemActions $component): array => $component->getExtraActions()
        ]);
    }

    public function getItems(): array
    {

        $items = [];

        foreach ($this->getRawState() ?? [] as $itemKey => $itemData) {
            $items[$itemKey] = $this
                ->getChildSchema()
                ->statePath($itemKey)
                ->constantState((is_array($itemData)) ? $itemData : null)
                ->inlineLabel(false)
                ->getClone();
        }

        return $items;
    }

    /**
     * @param array<Action|Closure> $actions
     */
    public function extraActions(array $actions): static
    {
        $this->extraActions = [
            ...$this->extraActions,
            ...$actions,
        ];

        return $this;
    }

    /**
     * @return array<Action>
     */
    public function getExtraActions(): array
    {
        return $this->cachedExtraActions ?? $this->cacheExtraActions();
    }

    /**
     * @return array<Action>
     */
    public function cacheExtraActions(): array
    {
        $this->cachedExtraActions = [];

        foreach ($this->extraActions as $extraAction) {
            foreach (Arr::wrap($this->evaluate($extraAction)) as $action) {
                $this->cachedExtraActions[$action->getName()] = $this->prepareAction(
                    $action
                        ->defaultColor('gray')
                        ->defaultSize(Size::Small)
                        ->defaultView(Action::ICON_BUTTON_VIEW),
                );
            }
        }

        return $this->cachedExtraActions;
    }

    public function getItemState(string $uuid): ?array
    {
        return $this->getRawState()[$uuid];
    }

    private function getRelatedModel(): ?string
    {
        return $this->relatedModel;
    }

    public function relatedModel(string $relatedModel): static
    {
        $this->relatedModel = $relatedModel;

        return $this;
    }
}
