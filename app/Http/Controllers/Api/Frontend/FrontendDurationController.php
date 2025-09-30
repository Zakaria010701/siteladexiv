<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Actions\Appointments\CalculateDuration;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FrontendDurationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            //'frontend' => ['required', 'integer'],
            'services' => ['required'],
            //'packages' => ['nullable'],
            'appointment_type' => ['required'],
        ]);

        $duration = CalculateDuration::make($request->appointment_type, explode(',', $request->services))->execute();

        return response()->json([
            'data' => ['duration' => $duration],
        ]);
    }
}
