<?php

use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use App\Models\Appointment;

use function Pest\Livewire\livewire;

it('can render index page', function () {
    /** @var \App\Models\User $user */
    $user = auth()->user();
    $user->givePermissionTo('view_any_appointment');
    $this->get(AppointmentResource::getUrl('index', panel: 'crm'))->assertSuccessful();
});

it('can list appointments', function () {
    /** @var \App\Models\User $user */
    $user = auth()->user();
    $user->givePermissionTo('view_any_appointment');

    $appointments = Appointment::factory()->count(10);
    livewire(Appointments\Pages\ListAppointments::class)
        ->assertCanSeeTableRecords($appointments);
});
