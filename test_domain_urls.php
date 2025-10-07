<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing URL generation for siteladexiv.test domain...\n\n";

// Simulate a request to siteladexiv.test
$_SERVER['HTTP_HOST'] = 'siteladexiv.test';
$_SERVER['REQUEST_SCHEME'] = 'http';

$filePath = '1/01K6AY3JV2CEB3H352AEYR30KW.webp';

echo "File path: {$filePath}\n";
echo "Storage::url(): " . \Illuminate\Support\Facades\Storage::url($filePath) . "\n";

// Test the domain generation logic
$storageUrl = \Illuminate\Support\Facades\Storage::url($filePath);
if (strpos($storageUrl, 'http') !== 0) {
    $storageUrl = 'http://siteladexiv.test' . $storageUrl;
}
echo "With siteladexiv.test domain: {$storageUrl}\n";

echo "\nRequest info:\n";
echo "Scheme: " . request()->getScheme() . "\n";
echo "Host: " . request()->getHost() . "\n";
echo "Full URL: " . request()->fullUrl() . "\n";

echo "\nTest completed!\n";