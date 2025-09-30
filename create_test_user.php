<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check if user_work_types table exists and get first record
try {
    $workType = DB::table('user_work_types')->first();
    $workTypeId = $workType ? $workType->id : 1;
} catch (Exception $e) {
    $workTypeId = 1;
}

$user = new App\Models\User();
$user->name = 'Test Admin';
$user->email = 'admin@test.com';
$user->password = bcrypt('password');
$user->user_work_type_id = $workTypeId;
$user->save();

echo "User created with ID: " . $user->id . "\n";
echo "Email: " . $user->email . "\n";
echo "Password: password\n";