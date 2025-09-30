<?php

namespace App\Filament\Crm\Pages;

use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use App\Enums\Appointments\AppointmentStatus;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use App\Filament\Crm\Resources\Customers\CustomerResource;
use App\Models\Appointment;
use App\Models\WaitingListEntry;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class OnlineAppointments extends Page implements HasTable
{
    use InteractsWithTable {
        makeTable as makeBaseTable;
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.crm.pages.online-appointments';

    public function mount(): void
    {
        $this->authorizeAccess();
    }

    protected function authorizeAccess(): void {}

    protected function configureDeleteAction(DeleteAction $action): void
    {
        $action
            ->authorize(fn (Model $record): bool => AppointmentResource::canDelete($record));
    }

    public static function getNavigationBadge(): ?string
    {
        return strval(Appointment::query()->status(AppointmentStatus::Pending)->count());
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Appointment::query()->status(AppointmentStatus::Pending))
            ->columns([
                TextColumn::make('branch.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('room.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('customer.full_name')
                    ->numeric()
                    ->url(fn (Appointment $record): string => isset($record->customer) ? CustomerResource::getUrl('edit', ['record' => $record->customer]) : '')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('appointmentOrder.status')
                    ->label(__('Payment'))
                    ->badge(),
                TextColumn::make('start')
                    ->dateTime(getDateFormat())
                    ->sortable(),
                TextColumn::make('end')
                    ->dateTime(getDateFormat())
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->requiresConfirmation()
                    ->action(fn (Appointment $record) => $record->markApproved()),
                ActionGroup::make([
                    EditAction::make()
                        ->authorize(fn (Model $record): bool => AppointmentResource::canEdit($record))
                        ->url(fn (Model $record): string => AppointmentResource::getUrl('edit', ['record' => $record])),
                    DeleteAction::make()
                        ->authorize(fn (Model $record): bool => AppointmentResource::canEdit($record)),
                ]),
            ])
            ->toolbarActions([
                BulkAction::make('approve')
                    ->requiresConfirmation()
                    ->action(fn (Collection $selectedRecords) => $selectedRecords->each(fn (Appointment $record) => $record->markApproved())),
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize(fn (): bool => AppointmentResource::canDeleteAny()),
                    ForceDeleteBulkAction::make()
                        ->authorize(fn (): bool => AppointmentResource::canForceDeleteAny()),
                ]),
            ]);
    }
}
