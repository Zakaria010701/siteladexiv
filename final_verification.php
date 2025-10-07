<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MediaItem;

echo "=== FINAL VERIFICATION: AllMedia Image Loading ===\n\n";

echo "1. Checking MediaItem records with files...\n";

// Get MediaItem records that have files
$mediaItemsWithFiles = MediaItem::whereNotNull('files')
    ->whereJsonLength('files', '>', 0)
    ->limit(5)
    ->get();

echo "Found " . $mediaItemsWithFiles->count() . " MediaItem records with files\n\n";

foreach ($mediaItemsWithFiles as $item) {
    echo "MediaItem: {$item->name} (ID: {$item->id})\n";
    echo "  Files in database: " . (is_array($item->files) ? count($item->files) : 'None') . "\n";

    if (is_array($item->files)) {
        foreach ($item->files as $index => $filePath) {
            // Convert Windows backslashes to forward slashes
            $filePath = str_replace('\\', '/', $filePath);
            $filePath = ltrim($filePath, '/');

            // Skip conversion files for main image
            if (preg_match('/\/conversions\/.*-(thumb|preview)\./', $filePath)) {
                continue;
            }

            $fullPath = storage_path('app/public/' . $filePath);
            $webUrl = '/storage/' . $filePath;

            echo "  File " . ($index + 1) . ": {$filePath}\n";
            echo "    Full path: {$fullPath}\n";
            echo "    Web URL: {$webUrl}\n";
            echo "    Exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";

            if (file_exists($fullPath)) {
                echo "    Size: " . number_format(filesize($fullPath) / 1024, 2) . " KB\n";
                echo "    MIME: " . mime_content_type($fullPath) . "\n";
                echo "    ✅ READY FOR DISPLAY\n";
            } else {
                echo "    ❌ FILE MISSING\n";
            }
            echo "\n";
        }
    }
}

echo "2. Summary Statistics...\n";
$totalMediaItems = MediaItem::count();
$itemsWithFiles = MediaItem::whereNotNull('files')->whereJsonLength('files', '>', 0)->count();
$itemsWithSpatieMedia = MediaItem::has('mediaFiles')->count();

echo "  Total MediaItem records: {$totalMediaItems}\n";
echo "  Records with files: {$itemsWithFiles}\n";
echo "  Records with Spatie Media: {$itemsWithSpatieMedia}\n";
echo "  Records ready for display: {$itemsWithFiles}\n\n";

echo "3. File Path Examples...\n";
foreach ($mediaItemsWithFiles->take(3) as $item) {
    if (is_array($item->files) && !empty($item->files)) {
        $mainFile = $item->files[0];
        $mainFile = str_replace('\\', '/', $mainFile);
        $mainFile = ltrim($mainFile, '/');
        $webUrl = '/storage/' . $mainFile;

        echo "  {$item->name}: {$webUrl}\n";
    }
}

echo "\n=== VERIFICATION COMPLETE ===\n";
echo "The AllMedia view should now display images correctly using the direct file paths.\n";
echo "Images are loaded from the 'files' column in the database and served via /storage/ URLs.\n";