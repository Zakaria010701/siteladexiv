<?php

namespace App\Filament\Admin\Resources\Todos\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Enums\Todos\TodoStatus;
use App\Models\Appointment;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\TodoItem;
use App\Models\Voucher;
use Filament\Forms;
use Filament\Forms\Components\MorphToSelect;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TodoItemRelationManager extends RelationManager
{
    protected static string $relationship = 'todoItems';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                MorphToSelect::make('subject')
                    ->searchable()
                    ->types([
                        Type::make(Customer::class)
                            ->titleAttribute('lastname')
                            ->searchColumns(['firstname', 'lastname'])
                            ->getOptionLabelFromRecordUsing(fn (Customer $record) => $record->full_name),
                        Type::make(Appointment::class)
                            ->titleAttribute('start')
                            ->getOptionLabelFromRecordUsing(fn (Appointment $record) => $record->title),
                        Type::make(Invoice::class)
                            ->titleAttribute('invoice_number'),
                        Type::make(Contract::class),
                        Type::make(Voucher::class),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                IconColumn::make('completed_at')
                    ->label(__('Completed'))
                    ->default(false)
                    ->boolean(),
                TextColumn::make('title')
                    ->grow(),
                TextColumn::make('subject')
                    ->url(fn (TodoItem $record) => $record->getSubjectUrl())
                    ->formatStateUsing(fn (TodoItem $record) => $record->getSubjectTitle()),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                Action::make('complete')
                    ->label(__('Done'))
                    ->icon('heroicon-o-check')
                    ->hidden(fn (TodoItem $record) => $record->completed)
                    ->action(function (TodoItem $record) {
                        $record->completed_at = now();
                        $record->save();

                        if($record->todo->todoItems()->completed()->count() == $record->todo->todoItems()->count()) {
                            $record->todo->status = TodoStatus::Done;
                            $record->todo->save();
                        }
                    }),
                ActionGroup::make([
                    EditAction::make(),
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
