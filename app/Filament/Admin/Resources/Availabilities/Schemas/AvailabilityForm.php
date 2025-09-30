<?php

namespace App\Filament\Admin\Resources\Availabilities\Schemas;

use App\Enums\TimeStep;
use App\Enums\Weekday;
use App\Filament\Admin\Resources\Availabilities\AvailabilityResource;
use App\Filament\Admin\Resources\Availabilities\Schemas\Components\AvailabilityAdvancedSettingsFieldset;
use App\Filament\Admin\Resources\Availabilities\Schemas\Components\AvailabilityDateFieldset;
use App\Filament\Admin\Resources\Availabilities\Schemas\Components\AvailabilityPlanableSelect;
use App\Filament\Admin\Resources\Availabilities\Schemas\Components\AvailabilityShiftsRepeater;
use App\Filament\Admin\Resources\Availabilities\Schemas\Components\AvailabilityTypeSelect;
use App\Models\Availability;
use App\Models\AvailabilityType;
use App\Models\Branch;
use App\Models\SystemResource;
use App\Models\User;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class AvailabilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([
                Section::make()
                    ->columns(3)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        AvailabilityTypeSelect::make(),
                        AvailabilityPlanableSelect::make(),
                        AvailabilityDateFieldset::make(),
                        AvailabilityAdvancedSettingsFieldset::make(),
                        AvailabilityShiftsRepeater::make(),
                        KeyValue::make('meta')
                            ->visible(AvailabilityResource::can('meta')),
                    ]),
            ]);
    }

    public static function configureModal(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([
                Section::make()
                    ->columns(3)
                    ->schema([
                        AvailabilityTypeSelect::make(),
                        AvailabilityDateFieldset::make(),
                        AvailabilityAdvancedSettingsFieldset::make(),
                        AvailabilityShiftsRepeater::make(),
                    ]),
            ]);
    }
}
