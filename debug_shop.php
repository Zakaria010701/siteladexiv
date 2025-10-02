<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get first few services from category 1 (Haarentfernung)
$category = App\Models\Category::find(1);
$services = $category->services()->limit(5)->get();

echo "Category: " . $category->name . "\n";
echo "Services found: " . $services->count() . "\n\n";

foreach($services as $service) {
    echo "ID: " . $service->id . "\n";
    echo "Name: " . $service->name . "\n";
    echo "Price: " . $service->price . "\n";
    echo "Description: " . $service->description . "\n";
    echo "Short code: " . $service->short_code . "\n";
    echo "---\n";
}