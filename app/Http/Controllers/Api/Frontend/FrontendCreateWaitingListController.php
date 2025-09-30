<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Actions\Appointments\AddToWaitingList;
use App\Actions\Customers\FindOrCreateCustomer;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class FrontendCreateWaitingListController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'branch' => ['required', 'integer'],
            'appointment_type' => ['required'],
            'category' => ['required', 'integer'],
            'packages' => ['nullable'],
            'services' => ['nullable'],
            'wish_date' => ['required', 'date', 'after_or_equal:today'],
            'wish_date_till' => ['required'],
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

            $waitingList = AddToWaitingList::make(
                wish_date: $request->wish_date,
                wish_date_till: $request->wish_date_till,
                appointmentType: $request->appointment_type,
                branch: $request->branch,
                category: $request->category,
                services: explode(',', $request->services),
                customer: $customer,
                provider: $request->provider,
            )->execute();
        } catch (Exception $e) {
            return response(content: ['message' => __('An Error Occured during Booking')], status: 500);
        }

        return response()->json([
            'data' => [
                'id' => $waitingList->id,
                'wish_date' => $waitingList->wish_date,
                'wish_date_till' => $waitingList->wish_date_till,
            ],
        ]);
    }
}
