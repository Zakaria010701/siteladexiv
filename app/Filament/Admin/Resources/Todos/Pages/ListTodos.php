<?php

namespace App\Filament\Admin\Resources\Todos\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Admin\Resources\Todos\TodoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTodos extends ListRecords
{
    protected static string $resource = TodoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $user = auth()->user();

        return [
            'all' => Tab::make()
                ->label(__('All')),
        ];
    }
}
