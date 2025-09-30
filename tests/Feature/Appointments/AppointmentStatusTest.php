<?php

use App\Enums\Appointments\AppointmentStatus;
use App\Events\Appointments as Events;
use App\Listeners\Appointments as Listeners;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Room;
use App\Models\User;
use App\Notifications\Appointments as Notifications;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->customer = Customer::factory()->create();
    $this->user = User::factory()->create();
    $this->appointment = \App\Models\Appointment::factory()
        ->for(Branch::first(), 'branch')
        ->for(Room::first(), 'room')
        ->for($this->user, 'user')
        ->for(Category::all()->random(), 'category')
        ->for($this->customer, 'customer')
        ->createQuietly();
});

describe('approve appointment', function () {
    it('can approve appointment', function () {
        Event::fake();

        expect($this->appointment->status)
            ->toBe(AppointmentStatus::Pending)
            ->and($this->appointment->approved_at)
            ->toBeNull();
        $this->appointment->markApproved();
        expect($this->appointment->status)
            ->toBe(AppointmentStatus::Approved)
            ->and($this->appointment->approved_at)
            ->not()->toBeNull();
    });

    it('dispatches appointment approved event', function () {
        Event::fake();

        $this->appointment->markApproved();

        Event::assertDispatched(Events\AppointmentApprovedEvent::class);

        Event::assertListening(
            Events\AppointmentApprovedEvent::class,
            Listeners\SendAppointmentApprovedNotification::class
        );
    });

    it('sends appointment approved notification', function () {
        Notification::fake();

        $this->appointment->markApproved();

        Notification::assertSentTo($this->customer, Notifications\AppointmentApprovedNotification::class);
    });
});

describe('cancel appointment', function () {
    it('can cancel appointments', function () {
        Event::fake();

        expect($this->appointment->status)
            ->toBe(AppointmentStatus::Pending)
            ->and($this->appointment->canceled_at)
            ->toBeNull();
        $this->appointment->markCanceled();
        expect($this->appointment->status)
            ->toBe(AppointmentStatus::Canceled)
            ->and($this->appointment->canceled_at)
            ->not()->toBeNull();
    });

    it('dispatches appointment canceled event', function () {
        Event::fake();

        $this->appointment->markCanceled();

        Event::assertDispatched(Events\AppointmentCanceledEvent::class);

        Event::assertListening(
            Events\AppointmentCanceledEvent::class,
            Listeners\SendAppointmentCanceledNotification::class
        );
    });

    it('sends appointment canceled notification', function () {
        Notification::fake();

        $this->appointment->markCanceled();

        Notification::assertSentTo($this->customer, Notifications\AppointmentCanceledNotification::class);
    });
});
