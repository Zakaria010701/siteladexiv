<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Branch;
use App\Models\Room;
use App\Models\Service;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

abstract class BaseResourceDependenciesWidget extends Widget
{

    public string $titleAttribute = 'name';

    protected string $view = 'filament.admin.widgets.resource-dependencies-widget';

    protected int|string|array $columnSpan = 'full';

    #[Computed]
    public abstract function records(): Collection;
    #[Computed]
    public abstract function record_count(): int;

    #[Computed]
    public abstract function relationships(): array;

    public abstract function model(): string;

    public abstract function getRecordUrl(Model $record): string;
}
