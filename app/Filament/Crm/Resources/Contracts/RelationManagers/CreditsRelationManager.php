<?php

namespace App\Filament\Crm\Resources\Contracts\RelationManagers;

use App\Filament\Crm\Resources\Contracts\RelationManagers\CreditsRelationManager;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use App\Models\Contract;
use App\Models\ServiceCredit;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CreditsRelationManager extends RelationManager
{
    protected static string $relationship = 'credits';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('price')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('price')
            ->defaultSort('service.name')
            ->columns([
                TextColumn::make('service.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('price')
                    ->sortable()
                    ->money('eur', locale: 'de'),
                TextColumn::make('source')
                    ->url(fn (ServiceCredit $credit) => $credit->getSourceUrl())
                    ->placeholder(__('No source'))
                    ->formatStateUsing(fn (ServiceCredit $credit) => $credit->getSourceTitle()),
                IconColumn::make('used_at')
                    ->label(__('Used'))
                    ->boolean()
                    ->default(false)
                    ->tooltip(fn (mixed $state) => $state ? formatDateTime($state) : null),
                TextColumn::make('usage')
                    ->url(fn (ServiceCredit $credit) => $credit->getUsageUrl())
                    ->placeholder(__('No usage'))
                    ->formatStateUsing(fn (ServiceCredit $credit) => $credit->getUsageTitle()),
            ])
            ->filters([
                TernaryFilter::make('used_at')
                    ->label(__('Used'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('used_at'),
                        false: fn (Builder $query) => $query->whereNull('used_at'),
                    ),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                ActionGroup::make([
                    Action::make('Convert')
                        ->hidden(fn (ServiceCredit $record) => $record->isUsed())
                        ->requiresConfirmation()
                        ->icon('heroicon-m-arrows-right-left')
                        ->action(function (ServiceCredit $record) {
                            $credit = $record->customer->customerCredits()->create([
                                'source_id' => $record->contract->id,
                                'source_type' => Contract::class,
                                'amount' => $record->price,
                            ]);

                            $record->used_at = now();
                            $record->usage_id = $credit->id;
                            $record->usage_type = $credit::class;
                            $record->save();

                            Notification::make()
                                ->title(__('status.result.success'))
                                ->success()
                                ->send();
                        }),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('Convert')
                        ->requiresConfirmation()
                        ->action(function (Collection $records, CreditsRelationManager $livewire) {
                            $records = $records->reject(fn (ServiceCredit $record) => $record->isUsed());
                            $total = $records->sum('price');
                            /** @var Contract $contract */
                            $contract = $livewire->getOwnerRecord();
                            $credit = $contract->customer->customerCredits()->create([
                                'source_id' => $contract->id,
                                'source_type' => Contract::class,
                                'amount' => $total,
                            ]);

                            $records->each(function (ServiceCredit $record) use ($credit) {
                                $record->used_at = now();
                                $record->usage_id = $credit->id;
                                $record->usage_type = $credit::class;
                                $record->save();
                            });

                            Notification::make()
                                ->title(__('status.result.success'))
                                ->success()
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
