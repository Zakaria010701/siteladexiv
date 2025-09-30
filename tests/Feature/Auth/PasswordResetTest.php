<?php

namespace Tests\Feature\Auth;

use App\Models\Customer;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Volt;

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response
        ->assertSeeVolt('pages.auth.forgot-password')
        ->assertStatus(200);
});

test('reset password link can be requested', function () {
    Notification::fake();

    $customer = Customer::factory()->create();

    Volt::test('pages.auth.forgot-password')
        ->set('email', $customer->email)
        ->call('sendPasswordResetLink');

    Notification::assertSentTo($customer, ResetPassword::class);
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $customer = Customer::factory()->create();

    Volt::test('pages.auth.forgot-password')
        ->set('email', $customer->email)
        ->call('sendPasswordResetLink');

    Notification::assertSentTo($customer, ResetPassword::class, function ($notification) {
        $response = $this->get('/reset-password/'.$notification->token);

        $response
            ->assertSeeVolt('pages.auth.reset-password')
            ->assertStatus(200);

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $customer = Customer::factory()->create();

    Volt::test('pages.auth.forgot-password')
        ->set('email', $customer->email)
        ->call('sendPasswordResetLink');

    Notification::assertSentTo($customer, ResetPassword::class, function ($notification) use ($customer) {
        $component = Volt::test('pages.auth.reset-password', ['token' => $notification->token])
            ->set('email', $customer->email)

            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('resetPassword');

        $component
            ->assertRedirect('/login')
            ->assertHasNoErrors();

        return true;
    });
});
