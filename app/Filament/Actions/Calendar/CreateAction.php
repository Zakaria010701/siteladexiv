<?php

namespace App\Filament\Actions\Calendar;

use Filament\Schemas\Schema;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use App\Filament\Widgets\BaseCalendarWidget;
use App\Models\Appointment;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction as BaseCreateAction;
use Illuminate\Database\Eloquent\Model;

class CreateAction extends BaseCreateAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->model(
            fn (BaseCalendarWidget $livewire) => $livewire->getModel()
        );

        $this->schema(
            fn (BaseCalendarWidget $livewire) => $livewire->getFormSchema()
        );

        $this->after(
            fn (BaseCalendarWidget $livewire) => $livewire->refreshRecords()
        );

        $this->action(function (array $arguments, Schema $schema): void {
            $model = $this->getModel();

            $record = $this->process(function (array $data, HasActions $livewire) use ($model): Model {
                if ($translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver()) {
                    $record = $translatableContentDriver->makeRecord($model, $data);
                } else {
                    $record = new $model;
                    $record->fill($data);
                }

                if ($relationship = $this->getRelationship()) {
                    /** @phpstan-ignore-next-line */
                    $relationship->save($record);

                    return $record;
                }

                $record->save();

                return $record;
            });

            $this->record($record);
            $schema->model($record)->saveRelationships();

            if ($arguments['another'] ?? false) {
                $this->callAfter();
                $this->sendSuccessNotification();

                $this->record(null);

                // Ensure that the form record is anonymized so that relationships aren't loaded.
                $schema->model($model);

                $schema->fill();

                $this->halt();

                return;
            }

            if ($arguments['edit'] ?? false) {
                $this->redirect(AppointmentResource::getUrl('edit', ['record' => $record]));
            }

            $this->success();
        });

        $this->extraModalFooterActions(function (): array {
            if ($this->getModel() == Appointment::class) {
                return [
                    $this->makeModalSubmitAction('createEdit', arguments: ['edit' => true])
                        ->label(__('Create & Edit')),
                    $this->makeModalSubmitAction('createAnother', arguments: ['another' => true])
                        ->label(__('filament-actions::create.single.modal.actions.create_another.label')),
                ];
            }

            return [
                $this->makeModalSubmitAction('createAnother', arguments: ['another' => true])
                    ->label(__('filament-actions::create.single.modal.actions.create_another.label')),
            ];

        });

        $this->cancelParentActions();
    }
}
