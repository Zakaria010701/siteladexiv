<?php

namespace App\Forms\Components;

use App\Forms\Components\TableRepeater\Concerns\CanBeStreamlined;
use App\Forms\Components\TableRepeater\Concerns\HasBreakPoints;
use App\Forms\Components\TableRepeater\Concerns\HasEmptyLabel;
use App\Forms\Components\TableRepeater\Concerns\HasExtraActions;
use App\Forms\Components\TableRepeater\Concerns\HasHeader;
use Filament\Actions\Action;
use Filament\Support\Enums\Size;
use Closure;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;

class TableRepeater extends Repeater
{
    use CanBeStreamlined;
    use HasBreakPoints;
    use HasEmptyLabel;
    use HasExtraActions;
    use HasHeader;

    protected bool|Closure|null $renderFooter = null;

    protected bool | Closure | null $showLabels = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerActions([
            fn (TableRepeater $component): array => $component->getExtraActions()
        ]);
    }

    public function getView(): string
    {
        return 'forms.components.table-repeater';
    }

    public function renderFooter(bool | Closure $condition = true): static
    {
        $this->renderFooter = $condition;

        return $this;
    }

    public function shouldRenderFooter(): bool
    {
        return $this->evaluate($this->renderFooter) ?? true;
    }

    public function getAddAction(): Action
    {
        return parent::getAddAction()->icon('heroicon-s-plus')->iconButton()->size(Size::Small)->color('primary');
    }

    public function getChildComponents(?string $key = null): array
    {
        $components = parent::getChildComponents();

        if ($this->shouldShowLabels()) {
            return $components;
        }

        foreach ($components as $component) {
            if (
                method_exists($component, 'hiddenLabel') &&
                ! $component instanceof Placeholder
            ) {
                $component->hiddenLabel();
            }
        }

        return $components;
    }

    public function showLabels(bool | Closure | null $condition = true): static
    {
        $this->showLabels = $condition;

        return $this;
    }

    public function shouldShowLabels(): bool
    {
        return $this->evaluate($this->showLabels) ?? false;
    }
}
