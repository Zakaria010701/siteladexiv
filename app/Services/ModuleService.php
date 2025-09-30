<?php

namespace App\Services;

use App\Models\ModuleSetting;
use Illuminate\Support\Facades\Cache;

class ModuleService
{
    private array $features;

    public function __construct()
    {
        $this->features = Cache::rememberForever(
            key: 'modules',
            callback: fn (): array => ModuleSetting::all()
                ->mapWithKeys(fn (ModuleSetting $item): array => [
                    $item->name => [
                        'active' => $item->active,
                    ],
                ])
                ->toArray()
        );
    }

    public function active(string $module): bool
    {
        return $this->features[$module]['active'] ?? false;
    }

    public function allAreActive(array $modules): bool
    {
        return collect($modules)->every(fn (string $module) => $this->active($module));
    }

    public function someAreActive(array $modules): bool
    {
        return collect($modules)->some(fn (string $module) => $this->active($module));
    }

    public function inactive(string $module): bool
    {
        return ! $this->active($module);
    }

    public function allAreInactive(array $modules): bool
    {
        return collect($modules)->every(fn (string $module) => $this->inactive($module));
    }

    public function someAreInactive(array $modules): bool
    {
        return collect($modules)->some(fn (string $module) => $this->inactive($module));
    }
}
