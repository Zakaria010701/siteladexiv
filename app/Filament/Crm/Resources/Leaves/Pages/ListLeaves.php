<?php

namespace App\Filament\Crm\Resources\Leaves\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Crm\Resources\Leaves\LeaveResource;
use App\Models\Leave;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListLeaves extends ListRecords
{
    protected static string $resource = LeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        if (auth()->user()->can('admin_leave')) {
            return [
                'own' => Tab::make(__('Own'))
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', auth()->id())),
                'open' => Tab::make(__('Open'))
                    ->badge(Leave::query()->unapproved()->notDenied()->count())
                    ->modifyQueryUsing(fn (Builder $query) => $query->unapproved()->notDenied()),
                'admin' => Tab::make(__('Admin')),
            ];
        }

        return [
            'own' => Tab::make(__('Own'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', auth()->id())),
        ];
    }
}
