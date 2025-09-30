<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Find the existing zakaria customer
    $zakariaCustomer = App\Models\Customer::where('email', 'zakaria@example.com')->first();

    if ($zakariaCustomer) {
        echo "Found existing zakaria customer!" . PHP_EOL;
        echo "Name: " . $zakariaCustomer->firstname . " " . $zakariaCustomer->lastname . PHP_EOL;
        echo "Email: " . $zakariaCustomer->email . PHP_EOL;

        // Update the customer with proper data
        $zakariaCustomer->update([
            'gender' => App\Enums\Gender::Male,
            'firstname' => 'Zakaria',
            'lastname' => 'Administrator',
            'name' => 'Zakaria Administrator',
            'phone_number' => '+49123456789',
            'birthday' => '1990-01-01',
            'prefered_contact_method' => App\Enums\Customers\ContactMethod::Email,
            'options' => [],
            'parent_id' => null,
            'meta' => [],
        ]);

        echo "Zakaria customer updated successfully!" . PHP_EOL;
        echo "You can now login with:" . PHP_EOL;
        echo "Email: zakaria@example.com" . PHP_EOL;
        echo "Password: password" . PHP_EOL;

    } else {
        echo "Zakaria customer not found. Creating new one..." . PHP_EOL;

        // Create zakaria customer
        $zakariaCustomer = App\Models\Customer::create([
            'gender' => App\Enums\Gender::Male,
            'firstname' => 'Zakaria',
            'lastname' => 'Administrator',
            'name' => 'Zakaria Administrator',
            'email' => 'zakaria@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'phone_number' => '+49123456789',
            'birthday' => '1990-01-01',
            'prefered_contact_method' => App\Enums\Customers\ContactMethod::Email,
            'options' => [],
            'parent_id' => null,
            'meta' => [],
        ]);

        echo "Zakaria customer created successfully!" . PHP_EOL;
        echo "Email: zakaria@example.com" . PHP_EOL;
        echo "Password: password" . PHP_EOL;
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}