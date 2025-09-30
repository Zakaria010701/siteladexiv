<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Support\Concerns\HasColor;

class ItemRepeater extends Repeater
{
    use HasColor;

    protected string $view = 'forms.components.item-repeater';

    protected bool|Closure $filled = false;

    protected string|array|Closure|null $textColor = null;

    protected string|Closure|bool|null $badge = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultColor(null);
    }

    public function filled(bool|Closure $condition = true): static
    {
        $this->filled = $condition;

        return $this;
    }

    public function badge(bool|string|Closure $badge): static
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * @param  string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null  $textColor
     */
    public function textColor(string|array|Closure|null $textColor): static
    {
        $this->textColor = $textColor;

        return $this;
    }

    public function getItemBadge(string $uuid): bool|string
    {
        if (is_bool($this->badge)) {
            return $this->badge;
        }
        if (is_string($this->badge)) {
            return $this->badge;
        }

        $container = $this->getChildComponentContainer($uuid);

        return $this->evaluate($this->badge, [
            'container' => $container,
            'state' => $container->getRawState(),
            'uuid' => $uuid,
        ]) ?? false;
    }

    public function getItemFilled(string $uuid): bool
    {
        if (is_bool($this->filled)) {
            return $this->filled;
        }

        $container = $this->getChildComponentContainer($uuid);

        return $this->evaluate($this->filled, [
            'container' => $container,
            'state' => $container->getRawState(),
            'uuid' => $uuid,
        ]) ?? false;
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    public function getItemColor(string $uuid): string|array|null
    {
        $container = $this->getChildComponentContainer($uuid);

        return $this->evaluate($this->color, [
            'container' => $container,
            'state' => $container->getRawState(),
            'uuid' => $uuid,
        ]) ?? $this->evaluate($this->defaultColor);
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    public function getItemTextColor(string $uuid): string|array|null
    {
        $container = $this->getChildComponentContainer($uuid);

        return $this->evaluate($this->textColor, [
            'container' => $container,
            'state' => $container->getRawState(),
            'uuid' => $uuid,
        ]);
    }
}
