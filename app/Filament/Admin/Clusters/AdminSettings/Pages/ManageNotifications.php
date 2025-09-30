<?php

namespace App\Filament\Admin\Clusters\AdminSettings\Pages;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use App\Enums\MailDriver;
use App\Filament\Admin\Clusters\AdminSettings\AdminSettingsCluster;
use App\Settings\NotificationSetting;
use App\Support\TemplateSupport;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Crypt;

class ManageNotifications extends SettingsPage
{

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = NotificationSetting::class;

    protected static ?string $cluster = AdminSettingsCluster::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('disable_notifications')
                    ->hint(__('Turn off all notifications sent from the application'))
                    ->inline(false)
                    ->required(),
                Toggle::make('send_email_notification_by_default')
                    ->inline(false)
                    ->required(),
                Toggle::make('send_sms_notification_by_default')
                    ->inline(false)
                    ->required(),
                Fieldset::make(__('Email From'))
                    ->schema([
                        TextInput::make('from_name')
                            ->hint(__('The name under which emails are sent'))
                            ->required(),
                        TextInput::make('from_email')
                            ->hint(__('The email from which emails are sent'))
                            ->required(),
                    ]),
                RichEditor::make('email_header')
                    ->hint(__('The header added to every email'))
                    ->extraInputAttributes(['style' => 'min-height: 16rem;'])
                    ->mergeTags(TemplateSupport::make()->getPlaceholderNames())
                    ->columnSpanFull()
                    ->json(),
                RichEditor::make('email_footer')
                    ->hint(__('The footer added to every email'))
                    ->extraInputAttributes(['style' => 'min-height: 16rem;'])
                    ->mergeTags(TemplateSupport::make()->getPlaceholderNames())
                    ->columnSpanFull()
                    ->json(),
                Fieldset::make(__('Email Service'))
                    ->columns(1)
                    ->schema([
                        Select::make('default_mailer')
                            ->hint(__('The default service for sending emails'))
                            ->live(onBlur: true)
                            ->options(MailDriver::class)
                            ->required(),
                        TextInput::make('smtp_host')
                            ->hint(__('The host for the smtp server'))
                            ->hidden(fn (Get $get) => $get('default_mailer') != MailDriver::Smtp),
                        TextInput::make('smtp_port')
                            ->hint(__('The port for the smtp server'))
                            ->hidden(fn (Get $get) => $get('default_mailer') != MailDriver::Smtp),
                        Select::make('smtp_encription')
                            ->hint(__('The encryption for the smtp server'))
                            ->options([
                                'tls' => 'TLS',
                                'ssl' => 'SSL',
                            ])
                            ->hidden(fn (Get $get) => $get('default_mailer') != MailDriver::Smtp),
                        TextInput::make('smtp_username')
                            ->hint(__('The username for the smtp server'))
                            ->hidden(fn (Get $get) => $get('default_mailer') != MailDriver::Smtp),
                        TextInput::make('smtp_password')
                            ->hint(__('The password for the smtp server'))
                            ->password()
                            ->revealable()
                            ->formatStateUsing(fn (?string $state): ?string => isset($state) ? Crypt::decrypt($state) : null)
                            ->dehydrateStateUsing(fn (?string $state): ?string => isset($state) ? Crypt::encrypt($state) : null)
                            ->hidden(fn (Get $get) => $get('default_mailer') != MailDriver::Smtp),
                    ]),
                Fieldset::make(__('SMS Service'))
                    ->columns(1)
                    ->schema([
                        TextInput::make('sms_77_api_key')
                            ->hint(str('['.__('The Api Key for Seven.io').'](https://www.seven.io/)')->inlineMarkdown()->toHtmlString()),
                        TextInput::make('sms_77_from')
                            ->hint(__('The name under which sms are send')),
                    ]),
            ]);
    }
}
