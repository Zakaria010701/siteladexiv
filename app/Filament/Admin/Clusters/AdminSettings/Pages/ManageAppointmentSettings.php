<?php

namespace App\Filament\Admin\Clusters\AdminSettings\Pages;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use App\Filament\Admin\Clusters\AdminSettings\AdminSettingsCluster;
use App\Settings\AppointmentSettings;
use Filament\Forms;
use Filament\Pages\SettingsPage;

class ManageAppointmentSettings extends SettingsPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string $settings = AppointmentSettings::class;

    protected static ?string $cluster = AdminSettingsCluster::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Fieldset::make(__('Appointment Duration'))
                            ->schema([
                                TextInput::make('min_appointment_duration')
                                    ->integer()
                                    ->required(),
                                TextInput::make('max_appointment_duration')
                                    ->integer()
                                    ->required(),
                                TextInput::make('default_treatment_duration')
                                    ->integer()
                                    ->required(),
                                TextInput::make('default_consultation_duration')
                                    ->integer()
                                    ->required(),
                                TextInput::make('default_treatment_consultation_duration')
                                    ->integer()
                                    ->required(),
                                TextInput::make('default_depriefing_duration')
                                    ->integer()
                                    ->required(),
                                TextInput::make('default_follow_up_duration')
                                    ->integer()
                                    ->required(),
                            ]),
                        Fieldset::make(__('Consultation Fee'))
                            ->columns(3)
                            ->schema([
                                Toggle::make('consultation_fee_enabled')
                                    ->helperText(__('If consultations have a flat fee'))
                                    ->inline(false)
                                    ->live()
                                    ->required(),
                                TextInput::make('consultation_fee')
                                    ->numeric()
                                    ->suffix('€')
                                    ->hidden(fn (Get $get) => ! $get('consultation_fee_enabled'))
                                    ->required(fn (Get $get) => $get('consultation_fee_enabled')),
                                Toggle::make('consultation_fee_credits_enabled')
                                    ->helperText(__('If consultation fees get converted to credits'))
                                    ->inline(false)
                                    ->hidden(fn (Get $get) => ! $get('consultation_fee_enabled'))
                                    ->required(fn (Get $get) => $get('consultation_fee_enabled')),
                            ]),
                    ]),

            ]);
    }
}
