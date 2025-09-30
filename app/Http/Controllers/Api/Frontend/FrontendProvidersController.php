<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Actions\Users\FindAvailableProviders;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProviderApiResource;
use Illuminate\Http\Request;

class FrontendProvidersController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'category' => 'required|integer',
            'branch' => 'required|integer',
            'services' => 'required',
            'appointment_type' => ['required'],
        ]);

        $providers = FindAvailableProviders::make(
            appointmentType: $request->appointment_type,
            branch: $request->branch,
            category: $request->category,
            services: explode(',', $request->services)
        )->execute();

        return ProviderApiResource::collection($providers);
    }
}
