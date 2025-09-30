<?php

namespace App\Filament\Admin\Clusters\AdminSettings\Pages;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use App\Filament\Admin\Clusters\AdminSettings\AdminSettingsCluster;
use App\Settings\CompanySettings;
use Filament\Forms;
use Filament\Pages\SettingsPage;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class ManageCompany extends SettingsPage
{

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = CompanySettings::class;

    protected static ?string $cluster = AdminSettingsCluster::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        PhoneInput::make('phone')
                            ->required(),
                        TextInput::make('email')
                            ->email()
                            ->required(),
                        Fieldset::make(__('Address'))
                            ->columns(3)
                            ->schema([
                                TextInput::make('location')
                                    ->required(),
                                TextInput::make('postcode')
                                    ->required(),
                                TextInput::make('address')
                                    ->required(),
                            ]),
                        TextInput::make('website'),
                        TextInput::make('vat_id'),
                        TextInput::make('tax_id'),
                        Fieldset::make(__('Bank Info'))
                            ->columns(3)
                            ->schema([
                                TextInput::make('bank_name'),
                                TextInput::make('bank_iban'),
                                TextInput::make('bank_bic'),
                            ]),
                        FileUpload::make('logo_path')
                            ->image()
                            ->imageEditor(),
                    ])
            ]);
    }
}
