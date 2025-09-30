<?php

namespace App\Filament\Crm\Concerns;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Enums\Contracts\ContractType;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use App\Filament\Crm\Resources\Contracts\ContractResource;
use App\Models\Contract;
use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Table;

trait HasContractsRelation
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->required(),
                Select::make('type')
                    ->required()
                    ->options(ContractType::class),
                TextInput::make('treatment_count')
                    ->live()
                    ->required()
                    ->numeric()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $set('price', collect($get('services'))->sum('price') * $get('treatment_count'));
                    }),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->suffix('€'),
                Repeater::make('services')
                    ->required()
                    ->columnSpanFull()
                    ->columns(3)
                    ->addable()
                    ->deletable()
                    ->reorderable(false)
                    ->schema([
                        Select::make('service_id')
                            ->required()
                            ->options(Service::query()->pluck('name', 'id'))
                            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                $price = Service::find($state)?->price ?? 0;
                                $set('price', $price);
                                $set('../../price', collect($get('../../services'))->sum('price') * $get('../../treatment_count'));
                            })
                            ->dehydrated()
                            ->searchable(),
                        TextInput::make('default_price')
                            ->disabled()
                            ->numeric(),
                        TextInput::make('price')
                            ->live()
                            ->required()
                            ->numeric()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $set('../../price', collect($get('../../services'))->sum('price') * $get('../../treatment_count'));
                            }),
                    ]),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                TextColumn::make('date')
                    ->date(getDateFormat())
                    ->sortable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('contractServices.badge')
                    ->badge(),
                TextColumn::make('price')
                    ->money('eur', locale: 'de')
                    ->sortable(),
                IconColumn::make('paid')
                    ->boolean()
                    ->state(fn (Contract $record): bool => $record->isPaid()),
                TextColumn::make('appointment.start')
                    ->numeric()
                    ->placeholder(__('No Appointment'))
                    ->url(fn (Contract $record) => ! is_null($record->appointment)
                        ? AppointmentResource::getUrl('edit', ['record' => $record->appointment])
                        : null)
                    ->dateTime(getDateTimeFormat())
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (Contract $record) => ContractResource::getUrl('edit', ['record' => $record])),
                ActionGroup::make([
                    Action::make('Download')
                        ->icon('heroicon-m-arrow-down-tray')
                        ->action(fn (Contract $record) => response()->streamDownload(function () use ($record) {
                            echo Pdf::loadView('pdf.contract', ['contract' => $record])->stream();
                        }, 'contract.pdf')),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
