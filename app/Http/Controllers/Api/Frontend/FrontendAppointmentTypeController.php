<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Enums\Appointments\AppointmentType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FrontendAppointmentTypeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return [
            'data' => collect(AppointmentType::getBookingTypes())
                ->map(fn ($case, $key) => [
                    'key' => $key,
                    'label' => $case,
                ])
                ->values(),
        ];
    }
}
