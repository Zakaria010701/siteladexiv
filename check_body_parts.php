<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get all services from category 1 (Haarentfernung)
$category = App\Models\Category::find(1);
$services = $category->services()->get();

echo "Services in Haarentfernung category:\n";
echo "Total: " . $services->count() . "\n\n";

foreach($services as $service) {
    echo "ID: " . $service->id . "\n";
    echo "Name: " . $service->name . "\n";
    echo "Short code: " . $service->short_code . "\n";
    echo "---\n";
}