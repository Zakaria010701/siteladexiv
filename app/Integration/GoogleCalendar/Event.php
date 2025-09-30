<?php

namespace App\Integration\GoogleCalendar;

use Google\Service\Calendar\ConferenceData;
use Google\Service\Calendar\ConferenceSolutionKey;
use Google\Service\Calendar\CreateConferenceRequest;
use Google\Service\Calendar\Event as CalendarEvent;
use Spatie\GoogleCalendar\Event as GoogleCalendarEvent;
use Illuminate\Support\Str;

class Event extends GoogleCalendarEvent
{
    /** @var CalendarEvent **/
    public $googleEvent;
}
