<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer; // ✅ Verwende das korrekte Modell
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ✅ Prüfen, ob die E-Mail bereits in der customers-Tabelle existiert
    public function checkEmailExistence(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $exists = Customer::where('email', $request->email)->exists();

        return response()->json(['exists' => $exists]);
    }

    // ✅ Login mit Customer-Modell
 public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $customer = Customer::where('email', $request->email)->first();

    if (!$customer || !Hash::check($request->password, $customer->password)) {
        throw ValidationException::withMessages([
            'email' => ['Die angegebenen Anmeldedaten sind ungültig.'],
        ]);
    }

    $token = $customer->createToken('api-token')->plainTextToken;

    $lastAppointment = $customer->appointments()
        ->with(['branch', 'category', 'treatmentType', 'services'])  // <== Hier RELATIONEN laden
        ->orderByDesc('created_at')
        ->first();

    return response()->json([
        'user' => $customer,
        'token' => $token,
        'last_appointment' => $lastAppointment,
    ]);
}

}