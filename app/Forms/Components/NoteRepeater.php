<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Support\Concerns\HasColor;
use Illuminate\Support\Carbon;

class NoteRepeater extends Repeater
{
    use HasColor;

    protected string $view = 'forms.components.note-repeater';

    protected ?Closure $itemDate = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultColor('grey');
    }

    public function itemDate(?Closure $date): static
    {
        $this->itemDate = $date;

        return $this;
    }

    public function getItemDate(string $uuid): ?string
    {
        $container = $this->getChildComponentContainer($uuid);

        /** @var Carbon|string|null */
        $date = $this->evaluate($this->itemDate, [
            'container' => $container,
            'state' => $container->getRawState(),
            'uuid' => $uuid,
        ]);

        return formatDate($date ?? now());
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
}
