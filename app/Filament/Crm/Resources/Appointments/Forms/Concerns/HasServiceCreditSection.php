<?php

namespace App\Filament\Crm\Resources\Appointments\Forms\Concerns;

use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Forms\Components\ItemRepeater;
use App\Models\Service;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;

trait HasServiceCreditSection
{
    private function getServiceCreditSection(): Section
    {
        return Section::make(__('Credits'))
            ->collapsible()
            ->compact()
            ->icon('heroicon-o-credit-card')
            //->hidden(fn (Get $get) => $this->moduleInactive(AppointmentModule::Notes, $get))
            ->schema([
                $this->getServicePackageCreditsRepeater(),
                $this->getServiceCreditRepeater(),
                $this->getCustomerCreditRepeater(),
            ]);
    }

    private function getServicePackageCreditsRepeater(): Repeater
    {
        return ItemRepeater::make('servicePackageCredits')
            ->label('')
            ->collapsed(true, false)
            ->reorderable(false)
            ->deletable(false)
            ->addable(false)
            ->itemLabel(fn (array $state): ?string => $state['name'])
            ->color(fn (array $state): string => 'primary')
            ->filled()
            ->textColor(Color::generateV3Palette('#ffffff'))
            ->extraItemActions([
                Action::make('use')
                    ->icon('heroicon-s-shopping-bag')
                    ->color(Color::generateV3Palette('#ffffff'))
                    ->action(function (array $arguments, Repeater $component, Get $get, Set $set) {
                        $data = $component->getItemState($arguments['item']);
                        $packages = $get('service_packages');
                        if (in_array($data['package'], $packages)) {
                            return;
                        }

                        if (empty($get('category_id'))) {
                            $set('category_id', $data['category']);
                        }

                        $packages[] = $data['package'];
                        $set('service_packages', $packages);

                        $services = Service::whereHas('servicePackages', fn (Builder $query) => $query->whereIn('service_packages.id', $packages))->get();
                        $set('services', $services->pluck('id')->toArray());
                        $this->updatedServices();
                    }),
            ])
            ->schema([
                TextInput::make('package')
                    ->numeric(),
                TextInput::make('category')
                    ->numeric(),
                TextInput::make('name'),
            ]);
    }

    private function getServiceCreditRepeater(): Repeater
    {
        return ItemRepeater::make('serviceCredits')
            ->label('')
            ->collapsed(true, false)
            ->reorderable(false)
            ->deletable(false)
            ->addable(false)
            ->filled()
            ->textColor(Color::generateV3Palette('#ffffff'))
            ->itemLabel(fn (array $state): ?string => $state['name'])
            ->badge(fn (array $state): null|string|bool => $state['open'] > 0 ? $state['open'] : false)
            ->color(fn (array $state): string => $state['open'] > 0 ? 'primary' : 'gray')
            ->extraItemActions([
                Action::make('use')
                    ->icon('heroicon-s-shopping-bag')
                    ->color(Color::generateV3Palette('#ffffff'))
                    ->action(function (array $arguments, Repeater $component, Get $get, Set $set) {
                        $data = $component->getItemState($arguments['item']);
                        $services = $get('services');
                        if (in_array($data['service'], $services)) {
                            return;
                        }

                        if (empty($get('category_id'))) {
                            $set('category_id', $data['category']);
                        }

                        $services[] = $data['service'];
                        $set('services', $services);

                        $this->calculate();
                    }),
            ])
            ->schema([
                TextInput::make('service')
                    ->numeric(),
                TextInput::make('category')
                    ->numeric(),
                TextInput::make('name'),
                TextInput::make('open')
                    ->numeric(),
            ]);
    }

    private function getCustomerCreditRepeater(): Repeater
    {
        return ItemRepeater::make('customerCredits')
            ->label('')
            ->collapsed(true, false)
            ->reorderable(false)
            ->deletable(false)
            ->addable(false)
            ->filled()
            ->textColor(Color::generateV3Palette('#ffffff'))
            ->color('primary')
            ->itemLabel(fn (array $state): ?string => $state['name'])
            ->badge(fn (array $state): null|string|bool => $state['open'] > 0 ? $state['open'] : false)
            ->schema([
                TextInput::make('name')
                    ->disabled(),
                TextInput::make('open')
                    ->disabled()
                    ->numeric(),
            ]);
    }
}
