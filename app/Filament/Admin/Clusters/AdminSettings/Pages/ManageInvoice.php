<?php

namespace App\Filament\Admin\Clusters\AdminSettings\Pages;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use App\Filament\Admin\Clusters\AdminSettings\AdminSettingsCluster;
use App\Settings\InvoiceSettings;
use App\Support\TemplateSupport;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Pages\SettingsPage;

class ManageInvoice extends SettingsPage
{

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = InvoiceSettings::class;

    protected static ?string $cluster = AdminSettingsCluster::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('invoice_series')
                            ->required(),
                        TextInput::make('proforma_series')
                            ->required(),
                        TextInput::make('offer_series')
                            ->required(),
                        TextInput::make('due_after_days')
                            ->integer()
                            ->required(),
                        TextInput::make('default_tax')
                            ->integer()
                            ->required(),
                        RichEditor::make('default_header')
                            ->hint(__('The default headerfor invoices'))
                            ->extraInputAttributes(['style' => 'min-height: 16rem;'])
                            ->mergeTags(TemplateSupport::make()->getPlaceholderNames())
                            ->columnSpanFull()
                            ->json(),
                        RichEditor::make('default_footer')
                            ->hint(__('The default footer for invoices'))
                            ->extraInputAttributes(['style' => 'min-height: 16rem;'])
                            ->mergeTags(TemplateSupport::make()->getPlaceholderNames())
                            ->columnSpanFull()
                            ->json(),
                    ])
            ]);
    }
}
