<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Filament\Admin\Resources\Availabilities\Schemas\Components\AvailabilityAdvancedSettingsFieldset;
use App\Filament\Admin\Resources\Availabilities\Schemas\Components\AvailabilityDateFieldset;
use App\Filament\Admin\Resources\Availabilities\Schemas\Components\AvailabilityShiftsRepeater;
use App\Filament\Admin\Resources\Availabilities\Schemas\Components\AvailabilityTypeSelect;
use App\Filament\Admin\Resources\Users\Schemas\Components\UserCurrentAvailabilityFieldset;
use App\Filament\Admin\Resources\Users\Schemas\Components\UserDetailsStep;
use App\Filament\Admin\Resources\Users\Schemas\Components\UserStep;
use App\Models\AvailabilityType;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserWizard {

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Wizard::make([
                    UserForm::step(),
                    UserDetailsForm::step(),
                    UserProviderForm::step(),
                    Step::make(__('Availability'))
                        ->icon(Heroicon::Clock)
                        ->schema([
                            UserCurrentAvailabilityFieldset::make(),
                        ])
                ])->skippable()->columnSpanFull(),
            ]);
    }
}
