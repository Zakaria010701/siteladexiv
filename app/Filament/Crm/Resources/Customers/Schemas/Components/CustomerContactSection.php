<?php

namespace App\Filament\Crm\Resources\Customers\Schemas\Components;

use App\Filament\Actions\Appointments\ContactCustomer;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Builder;

class CustomerContactSection
{
    public static function make(): Section
    {
        return Section::make(__('Contact'))
            ->compact()
            ->collapsible()
            ->schema([
                Actions::make([
                    ContactCustomer::make(),
                ]),
                Repeater::make('customerContacts')
                    ->relationship('customerContacts', fn (Builder $query) => $query->latest()->limit(3))
                    ->addable(false)
                    ->deletable(false)
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('message'),
                    ]),
            ]);
    }
}
