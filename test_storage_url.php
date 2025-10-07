<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing storage URL generation...\n\n";

// Test different URL generation methods
$filePath = '1/01K6AY3JV2CEB3H352AEYR30KW.webp';

echo "File path: {$filePath}\n";
echo "asset('storage/' . \$filePath): " . asset('storage/' . $filePath) . "\n";
echo "asset('storage') . '/' . \$filePath: " . asset('storage') . '/' . $filePath . "\n";

echo "Storage::disk('public')->exists(): " . (\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath) ? 'YES' : 'NO') . "\n";

try {
    echo "Storage::url(): " . \Illuminate\Support\Facades\Storage::url($filePath) . "\n";
} catch (Exception $e) {
    echo "Storage::url() error: " . $e->getMessage() . "\n";
}

echo "\nFile exists check:\n";
$fullPath = storage_path('app/public/' . $filePath);
echo "Full path: {$fullPath}\n";
echo "File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
echo "File size: " . (file_exists($fullPath) ? filesize($fullPath) . ' bytes' : 'N/A') . "\n";

echo "\nDirect file access test:\n";
$directUrl = 'http://localhost:8000/storage/' . $filePath;
echo "Direct URL: {$directUrl}\n";

// Test if we can read the file content directly
if (file_exists($fullPath)) {
    $fileContent = file_get_contents($fullPath);
    echo "Can read file content: " . (strlen($fileContent) > 0 ? 'YES' : 'NO') . "\n";
    echo "Content length: " . strlen($fileContent) . " bytes\n";
}

echo "\nTest completed!\n";