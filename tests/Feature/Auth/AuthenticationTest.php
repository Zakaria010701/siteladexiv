<?php

use App\Models\Customer;
use Livewire\Volt\Volt;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response
        ->assertOk()
        ->assertSeeVolt('pages.auth.login');
});

test('customer can authenticate using the login screen', function () {
    $customer = Customer::factory()->create();

    $component = Volt::test('pages.auth.login')
        ->set('email', $customer->email)
        ->set('password', 'password');

    $component->call('login');

    $component
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('customers can not authenticate with invalid password', function () {
    $customer = Customer::factory()->create();

    $component = Volt::test('pages.auth.login')
        ->set('email', $customer->email)
        ->set('password', 'wrong-password');

    $component->call('login');

    $component
        ->assertHasErrors()
        ->assertNoRedirect();

    $this->assertGuest();
});

test('navigation menu can be rendered', function () {
    $customer = Customer::factory()->create();

    $this->actingAs($customer);

    $response = $this->get('/dashboard');

    $response
        ->assertOk()
        ->assertSeeVolt('layout.navigation');
});

test('users can logout', function () {
    $customer = Customer::factory()->create();

    $this->actingAs($customer);

    $component = Volt::test('layout.navigation');

    $component->call('logout');

    $component
        ->assertHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
});
