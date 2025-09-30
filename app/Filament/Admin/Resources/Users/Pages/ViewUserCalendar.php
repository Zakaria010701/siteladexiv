<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\Widgets\UserCalendar;
use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class ViewUserCalendar extends Page
{
    use InteractsWithRecord;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    protected static string $resource = UserResource::class;

    protected string $view = 'filament.admin.resources.user-resource.pages.view-user-calendar';

    protected function getHeaderWidgets(): array
    {
        return [
            UserCalendar::class
        ];
    }
}
