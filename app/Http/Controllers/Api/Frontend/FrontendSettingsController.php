<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FrontendSettingsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return response()->json([
            'data' => [
                'slot_step' => frontend()->slot_step,
                'email_required' => frontend()->email_required,
                'phone_number_required' => frontend()->phone_number_required,
            ],
        ]);
    }
}
