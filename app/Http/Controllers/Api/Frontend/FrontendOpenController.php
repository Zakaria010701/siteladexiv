<?php

namespace App\Http\Controllers\Api\Frontend;

use App\DataObjects\Calendar\CalendarOpening;
use App\Http\Controllers\Controller;
use App\Http\Resources\CalendarOpeningApiResource;
use App\Support\Appointment\BookingCalculator;
use App\Support\Calendar\CalendarOpeningCalculator;
use Illuminate\Http\Request;

class FrontendOpenController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $open = CalendarOpeningCalculator::make(
            start: $request->start,
            end: $request->end,
            appointmentType: $request->appointment_type,
            services: explode(',', $request->services),
            branches: $request->has('branches') ? explode(',', $request->branches) : null,
            rooms: $request->has('rooms') ? explode(',', $request->rooms) : null,
            users: $request->has('users') ? explode(',', $request->users) : null,
            resources: $request->has('resources') ? explode(',', $request->resources) : null,
            duration: $request->has('duration') ? $request->duration : null,
        )
        ->findCalendarOpenings();

        return CalendarOpeningApiResource::collection($open);
    }
}
