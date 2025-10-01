<?php

use App\Livewire\Booking;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('main');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth:customers', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth:customers'])
    ->name('profile');



Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [\App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/details', [\App\Http\Controllers\CartController::class, 'getDetails'])->name('cart.details');

Route::get('/{slug}', \App\Http\Controllers\PageController::class)
    ->where('slug', '[A-Za-z0-9\-]+')
    ->name('cms.page');

Route::post('/cms/contact-form/submit', [\App\Http\Controllers\PageController::class, 'submitContactForm'])
    ->name('cms.contact-form.submit');

Route::get('/booking', Booking::class)->name('booking');
Route::get('/history', \App\Livewire\Appointments::class)->name('history');


Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [\App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');

/*Route::get('/notification/{type}', function (NotificationType $type) {
    $appointment = Appointment::first();

    return match ($type) {
        NotificationType::AppointmentApproved => (new AppointmentApprovedNotification($appointment))->toMail($appointment->customer),
        default => null
    };
});*/

require __DIR__.'/auth.php';
