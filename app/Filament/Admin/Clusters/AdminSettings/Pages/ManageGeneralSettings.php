<?php

namespace App\Filament\Admin\Clusters\AdminSettings\Pages;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use App\Filament\Admin\Clusters\AdminSettings\AdminSettingsCluster;
use App\Settings\GeneralSettings as SettingsGeneralSettings;
use Filament\Forms;
use Filament\Pages\SettingsPage;
use Filament\Support\Icons\Heroicon;

class ManageGeneralSettings extends SettingsPage
{
    //protected string $view = 'filament.admin.pages.settings-page';

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string $settings = SettingsGeneralSettings::class;

    protected static ?string $cluster = AdminSettingsCluster::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('date_format')
                            ->required(),
                        TextInput::make('time_format')
                            ->required(),
                        TextInput::make('default_time_slot')
                            ->required()
                            ->numeric(),
                        TextInput::make('default_appointment_time')
                            ->required()
                            ->numeric(),
                    ])
            ]);
    }
}
