<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Actions\Appointments\BookAppointment;
use App\Actions\Customers\FindOrCreateCustomer;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class FrontendCreateAppointmentController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'room' => ['required', 'integer'],
            'appointment_type' => ['required'],
            'category' => ['required', 'integer'],
            'services' => ['required'],
            'start' => ['required', 'date', 'after_or_equal:today'],
            'gender' => ['required'],
            'providers' => ['nullable'],
            'firstname' => ['required'],
            'lastname' => ['required'],
            'phone_number' => frontend()->phone_number_required ? ['required', 'numeric', 'starts_with:0'] : ['nullable', 'numeric', 'starts_with:0'],
            'email' => ['required', 'email'],
        ]);

        try {
            $customer = FindOrCreateCustomer::make([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'gender' => $request->gender,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
            ])->execute();

            $appointment = BookAppointment::make(
                date: $request->start,
                appointmentType: $request->appointment_type,
                room: $request->branch,
                customer: $customer,
                user: $request->user,
                category: $request->category,
                services: explode(',', $request->services),
                resources: $request->has('resources') ? explode(',', $request->resources) : null,
                duration: $request->has('duration') ? $request->duration : null,
            )->execute();
        } catch (Exception $e) {
            return response(content: ['message' => __('An Error Occured during Booking')], status: 500);
        }

        return response()->json([
            'data' => [
                'start' => $appointment->start->toISOString(),
                'end' => $appointment->end->toISOString(),
            ],
        ]);
    }
}
