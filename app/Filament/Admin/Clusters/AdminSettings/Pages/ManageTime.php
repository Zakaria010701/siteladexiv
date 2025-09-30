<?php

namespace App\Filament\Admin\Clusters\AdminSettings\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Fieldset;
use App\Filament\Admin\Clusters\AdminSettings\AdminSettingsCluster;
use App\Settings\TimeSettings;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;

class ManageTime extends SettingsPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = TimeSettings::class;

    protected static ?string $cluster = AdminSettingsCluster::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make(__('Break'))
                    ->schema([
                        TextInput::make('two_hour_break')
                            ->label(__('settings.time.label.hour_break', ['hours' => 2]))
                            ->helperText(__('settings.time.helper.hour_break', ['hours' => 2]))
                            ->hint(__('settings.time.hint.in_minutes'))
                            ->required()
                            ->numeric(),
                        TextInput::make('four_hour_break')
                            ->label(__('settings.time.label.hour_break', ['hours' => 4]))
                            ->helperText(__('settings.time.helper.hour_break', ['hours' => 4]))
                            ->hint(__('settings.time.hint.in_minutes'))
                            ->required()
                            ->numeric(),
                        TextInput::make('six_hour_break')
                            ->label(__('settings.time.label.hour_break', ['hours' => 6]))
                            ->helperText(__('settings.time.helper.hour_break', ['hours' => 6]))
                            ->hint(__('settings.time.hint.in_minutes'))
                            ->required()
                            ->numeric(),
                        TextInput::make('eight_hour_break')
                            ->label(__('settings.time.label.hour_break', ['hours' => 8]))
                            ->helperText(__('settings.time.helper.hour_break', ['hours' => 8]))
                            ->hint(__('settings.time.hint.in_minutes'))
                            ->required()
                            ->numeric(),
                        TextInput::make('ten_hour_break')
                            ->label(__('settings.time.label.hour_break', ['hours' => 10]))
                            ->helperText(__('settings.time.helper.hour_break', ['hours' => 10]))
                            ->hint(__('settings.time.hint.in_minutes'))
                            ->required()
                            ->numeric(),
                    ]),
                Fieldset::make(__('Status'))
                    ->schema([
                        TextInput::make('early_check_in_minutes')
                            ->label(__('settings.time.label.early_check_in'))
                            ->helperText(__('settings.time.helper.early_check_in'))
                            ->hint(__('settings.time.hint.in_minutes'))
                            ->required()
                            ->numeric(),
                        TextInput::make('late_check_in_minutes')
                            ->label(__('settings.time.label.late_check_in'))
                            ->helperText(__('settings.time.helper.late_check_in'))
                            ->hint(__('settings.time.hint.in_minutes'))
                            ->required()
                            ->numeric(),
                        TextInput::make('early_check_out_minutes')
                            ->label(__('settings.time.label.early_check_out'))
                            ->helperText(__('settings.time.helper.early_check_out'))
                            ->hint(__('settings.time.hint.in_minutes'))
                            ->required()
                            ->numeric(),
                        TextInput::make('late_check_out_minutes')
                            ->label(__('settings.time.label.late_check_out'))
                            ->helperText(__('settings.time.helper.late_check_out'))
                            ->hint(__('settings.time.hint.in_minutes'))
                            ->required()
                            ->numeric(),
                    ]),
                Fieldset::make(__('Worktime'))
                    ->schema([
                        Toggle::make('worktime_prevent_check_in_before')
                            ->label(__('settings.time.label.wt_prevent_check_in'))
                            ->helperText(__('settings.time.helper.wt_prevent_check_in'))
                            ->required()
                            ->inline(false),
                        TextInput::make('worktime_prevent_check_in_before_minutes')
                            ->label(__('settings.time.label.wt_prevent_check_in_minutes'))
                            ->helperText(__('settings.time.helper.wt_prevent_check_in_minutes'))
                            ->hint(__('settings.time.hint.in_minutes'))
                            ->required()
                            ->numeric(),
                        Toggle::make('worktime_auto_logout_users')
                            ->label(__('settings.time.label.worktime_auto_logout_users'))
                            ->helperText(__('settings.time.helper.worktime_auto_logout_users'))
                            ->required()
                            ->inline(false),
                        TextInput::make('worktime_auto_logout_after_minutes')
                            ->label(__('settings.time.label.worktime_auto_logout_after_minutes'))
                            ->helperText(__('settings.time.helper.worktime_auto_logout_after_minutes'))
                            ->hint(__('settings.time.hint.in_minutes'))
                            ->required()
                            ->numeric(),
                    ]),
                Fieldset::make(__('Target time'))
                    ->schema([
                        Toggle::make('target_auto_logout_users')
                            ->label(__('settings.time.label.target_auto_logout_users'))
                            ->helperText(__('settings.time.helper.target_auto_logout_users'))
                            ->required()
                            ->inline(false),
                        TextInput::make('target_auto_logout_after_minutes')
                            ->label(__('settings.time.label.target_auto_logout_after_minutes'))
                            ->helperText(__('settings.time.helper.target_auto_logout_after_minutes'))
                            ->hint(__('settings.time.hint.in_minutes'))
                            ->required()
                            ->numeric(),
                    ]),
                Fieldset::make(__('Overtime'))
                    ->schema([
                        Toggle::make('overtime_cap_enabled')
                            ->label(__('settings.time.label.overtime_cap_enabled'))
                            ->helperText(__('settings.time.helper.overtime_cap_enabled'))
                            ->required()
                            ->inline(false),
                        TextInput::make('overtime_cap_minutes')
                            ->label(__('settings.time.label.overtime_cap_minutes'))
                            ->helperText(__('settings.time.helper.overtime_cap_minutes'))
                            ->hint(__('settings.time.hint.in_minutes'))
                            ->required()
                            ->numeric(),
                    ]),
            ]);
    }
}
