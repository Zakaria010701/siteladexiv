<?php

namespace App\Livewire;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use App\Models\Appointment;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class Appointments extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function mount()
    {
        if (! frontend()->customer_login_enabled) {
            $this->redirect('/');
        }
    }

    public function render()
    {
        return view('livewire.appointments');
    }

    public function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->query(Appointment::query()->where('customer_id', auth()->user()->id))
            ->columns([
                TextColumn::make('start')
                    ->label(__('Date'))
                    ->date(getDateTimeFormat())
                    ->toggleable(false),
                TextColumn::make('type')
                    ->badge()
                    ->toggleable(false),
                TextColumn::make('status')
                    ->badge()
                    ->toggleable(false),
                TextColumn::make('appointmentItems.description')
                    ->badge()
                    ->toggleable(false),
            ])
            ->filters([
                // ...
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('cancel')
                        ->label(__('Cancel Appointment'))
                        ->requiresConfirmation()
                        ->hidden(function (Appointment $record) {
                            if (! frontend()->appointment_cancelation_enabled) {
                                return true;
                            }

                            $before = frontend()->appointment_cancelation_before_step->add(
                                now(),
                                frontend()->appointment_cancelation_before_time
                            );

                            if ($before->gte($record->start)) {
                                return true;
                            }

                            return false;
                        })
                        ->action(fn (Appointment $record) => $record->markCanceled(true)),
                ]),
            ])
            ->toolbarActions([
                // ...
            ]);
    }
}
