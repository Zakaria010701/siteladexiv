<?php

namespace App\Filament\Admin\Clusters\AdminSettings\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use App\Filament\Admin\Clusters\AdminSettings\AdminSettingsCluster;
use App\Settings\IntegrationSettings;
use Filament\Forms;
use Filament\Pages\SettingsPage;

class ManageIntegration extends SettingsPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = IntegrationSettings::class;

    protected static ?string $cluster = AdminSettingsCluster::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Google'))
                    ->compact()
                    ->schema([
                        Toggle::make('google_sync_calendar')
                            ->inline(false),
                        TextInput::make('google_calendar_id'),
                        FileUpload::make('google_credentials_json_path')
                            ->acceptedFileTypes([
                                'application/json',
                            ]),
                    ]),
            ]);
    }
}
