<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$categories = App\Models\Category::withCount('services')->get();
foreach($categories as $category) {
    echo $category->id . ': ' . $category->name . ' (' . $category->services_count . ' services)' . PHP_EOL;
}