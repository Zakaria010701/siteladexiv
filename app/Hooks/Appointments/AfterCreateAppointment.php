<?php

namespace App\Hooks\Appointments;

use App\Events\Appointments\AppointmentApprovedEvent;
use App\Events\Appointments\AppointmentPendingEvent;
use App\Integration\GoogleCalendar\Event;
use App\Models\Appointment;

class AfterCreateAppointment
{
    public function __construct(private Appointment $appointment) {}

    public static function make(Appointment $appointment): self
    {
        return new self($appointment);
    }

    public function execute(): Appointment
    {
        $this->changeParticipantToCustomer();
        $this->createGoogleEvent();
        $this->appointment->saveQuietly();

        $this->dispatchStatusEvents();

        return $this->appointment;
    }

    private function changeParticipantToCustomer(): void
    {
        if (! is_null($this->appointment->customer)) {
            return;
        }

        if ($this->appointment->participants->isEmpty()) {
            return;
        }

        $this->appointment->customer_id = $this->appointment->participants->first()->id;
    }

    private function dispatchStatusEvents(): void
    {
        if ($this->appointment->status->isPending()) {
            AppointmentPendingEvent::dispatch($this->appointment, auth()->user(), true);
        }

        if ($this->appointment->status->isApproved()) {
            AppointmentApprovedEvent::dispatch($this->appointment, auth()->user(), true);
        }
    }

    private function createGoogleEvent(): void
    {
        if(!integration()->google_sync_calendar) {
            return;
        }

        $event = new Event;
        $event->name = $this->appointment->title;
        $event->startDateTime = $this->appointment->start;
        $event->endDateTime = $this->appointment->end;
        // Both adding attendees and adding meet links require domain wide auth
        // https://developers.google.com/workspace/calendar/api/v3/reference/events?hl=de
        // https://console.cloud.google.com/
        /*$event->addAttendee([
            'email' => $this->appointment->customer->email,
            'name' => $this->appointment->customer->full_name,
            'responseStatus' => 'needsAction',
        ]);
        $event->addMeetLink();*/
        $event = $event->save();

        $this->appointment->google_event_id = $event->id;
    }
}
