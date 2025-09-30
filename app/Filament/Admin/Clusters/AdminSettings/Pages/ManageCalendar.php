<?php

namespace App\Filament\Admin\Clusters\AdminSettings\Pages;

use App\Filament\Admin\Clusters\AdminSettings\AdminSettingsCluster;
use App\Settings\CalendarSettings;
use BackedEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageCalendar extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string $settings = CalendarSettings::class;

    protected static ?string $cluster = AdminSettingsCluster::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Fieldset::make()
                            ->schema([
                                Toggle::make('group_resources_in_day_plan')
                                    ->required(),
                                ColorPicker::make('resource_group_color'),
                                Toggle::make('group_users_in_day_plan')
                                    ->required(),
                                ColorPicker::make('user_group_color'),
                            ]),
                        Fieldset::make()
                            ->schema([
                                TimePicker::make('slot_duration')
                                    ->required(),
                                TimePicker::make('slot_label_interval')
                                    ->required(),
                            ]),
                        Toggle::make('now_indicator')
                            ->required(),
                        Toggle::make('dates_above_resources')
                            ->required(),
                    ]),
            ]);
    }
}
