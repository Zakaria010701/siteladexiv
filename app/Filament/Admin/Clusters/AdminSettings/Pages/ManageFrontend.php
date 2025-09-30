<?php

namespace App\Filament\Admin\Clusters\AdminSettings\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use App\Enums\TimeStep;
use App\Filament\Admin\Clusters\AdminSettings\AdminSettingsCluster;
use App\Settings\FrontendSetting;
use Filament\Forms;
use Filament\Pages\SettingsPage;

class ManageFrontend extends SettingsPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = FrontendSetting::class;

    protected static ?string $cluster = AdminSettingsCluster::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Booking'))
                    ->compact()
                    ->schema([
                        TextInput::make('slot_step')
                            ->hint(__('In which steps to show time slots'))
                            ->required()
                            ->numeric(),
                        TextInput::make('min_duration')
                            ->hint(__('The min duration of an online appointment'))
                            ->required()
                            ->numeric(),
                        TextInput::make('max_duration')
                            ->hint(__('The max duration of an online appointment'))
                            ->required()
                            ->numeric(),
                        Fieldset::make(__('Booking Constraints'))
                            ->columns(2)
                            ->schema([
                                Toggle::make('booking_constraint_by_category')
                                    ->hint(__('Booking is only possible to users that provide the selected category'))
                                    ->inline(false),
                                Toggle::make('booking_constraint_by_services')
                                    ->hint(__('Booking is only possible to users that provide the selected services'))
                                    ->inline(false),
                            ]),
                        Fieldset::make(__('Booking Requirements'))
                            ->columns(2)
                            ->schema([
                                Toggle::make('email_required')
                                    ->inline(false),
                                Toggle::make('phone_number_required')
                                    ->inline(false),
                            ]),
                    ]),
                Section::make(__('Customer Login'))
                    ->compact()
                    ->schema([
                        Toggle::make('customer_login_enabled')
                            ->inline(false),
                        Fieldset::make(__('Cancelation'))
                            ->columns(3)
                            ->schema([
                                Toggle::make('appointment_cancelation_enabled')
                                    ->inline(false),
                                TextInput::make('appointment_cancelation_before_time')
                                    ->numeric()
                                    ->requiredIf('appointment_cancelation_enabled', true),
                                Select::make('appointment_cancelation_before_step')
                                    ->options(TimeStep::class)
                                    ->requiredIf('appointment_cancelation_enabled', true),
                            ]),
                        Fieldset::make(__('Reschedule'))
                            ->columns(3)
                            ->schema([
                                Toggle::make('appointment_reschedule_enabled')
                                    ->inline(false),
                                TextInput::make('appointment_reschedule_before_time')
                                    ->numeric()
                                    ->requiredIf('appointment_reschedule_enabled', true),
                                Select::make('appointment_reschedule_before_step')
                                    ->options(TimeStep::class)
                                    ->requiredIf('appointment_reschedule_enabled', true),
                            ]),
                    ]),
            ]);
    }
}
