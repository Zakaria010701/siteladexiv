<?php

namespace App\Filament\Crm\Resources\Invoices\Schemas;

use App\Enums\Invoices\InvoiceStatus;
use App\Enums\Invoices\InvoiceType;
use App\Filament\Crm\Resources\Invoices\InvoiceResource;
use App\Filament\Crm\Resources\Invoices\Schemas\Components\InvoiceDatePicker;
use App\Filament\Crm\Resources\Invoices\Schemas\Components\InvoiceItemsRepeater;
use App\Filament\Crm\Resources\Invoices\Schemas\Components\InvoiceNumberComponent;
use App\Filament\Crm\Resources\Invoices\Schemas\Components\InvoiceRecipientSelect;
use App\Filament\Crm\Resources\Invoices\Schemas\Components\InvoiceSourceSelect;
use App\Filament\Crm\Resources\Invoices\Schemas\Components\InvoiceTypeSelect;
use App\Forms\Components\FusedMorphToSelect;
use App\Forms\Components\TableRepeater;
use App\Forms\Components\TableRepeater\Header;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Service;
use App\Support\Calculator;
use App\Support\TemplateSupport;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Invoice'))
                    ->compact()
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        InvoiceRecipientSelect::make(),
                        InvoiceSourceSelect::make(),
                        InvoiceTypeSelect::make(),
                        InvoiceNumberComponent::make(),
                        TextInput::make('invoice_number')
                            ->required()
                            ->readOnly()
                            ->hiddenOn('create')
                            ->readOnly()
                            ->maxLength(255),
                        InvoiceDatePicker::make(),
                        DatePicker::make('due_date')
                            ->default(today()->addDays(invoice()->due_after_days)),
                        RichEditor::make('header')
                            ->mergeTags(TemplateSupport::make()->getPlaceholderNames())
                            ->columnSpanFull()
                            ->default(invoice()->default_header)
                            ->json(),
                        RichEditor::make('footer')
                            ->mergeTags(TemplateSupport::make()->getPlaceholderNames())
                            ->columnSpanFull()
                            ->default(invoice()->default_footer)
                            ->json(),
                        Textarea::make('note'),
                        KeyValue::make('meta')
                            ->visible(InvoiceResource::can('meta')),
                    ]),
                Section::make(__('Items'))
                    ->compact()
                    ->collapsible()
                    ->schema([
                        InvoiceItemsRepeater::make(),
                        TextEntry::make('gross_total')
                            ->columnSpanFull()
                            ->extraEntryWrapperAttributes(['class' => 'justify-center'])
                            ->state(fn (Get $get) => formatMoney(collect($get('items'))->sum('sub_total'))),
                    ]),
            ]);
    }


}
