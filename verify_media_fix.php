<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MediaItem;
use Illuminate\Support\Facades\Log;

echo "Verifying MediaItem-Spatie Media associations...\n\n";

// Check a few MediaItem records to verify they now have Spatie Media associations
$testIds = [3, 8, 20, 35, 41, 53];

foreach ($testIds as $id) {
    $mediaItem = MediaItem::find($id);

    if ($mediaItem) {
        echo "MediaItem {$id}: {$mediaItem->name}\n";
        echo "  - Files in database: " . (is_array($mediaItem->files) ? count($mediaItem->files) : 'None') . "\n";
        echo "  - Spatie Media objects: " . $mediaItem->mediaFiles()->count() . "\n";

        if ($mediaItem->mediaFiles()->count() > 0) {
            $mediaFile = $mediaItem->mediaFiles->first();
            echo "  - First media URL: " . $mediaFile->getUrl() . "\n";
            echo "  - File exists: " . (file_exists($mediaFile->getPath()) ? 'Yes' : 'No') . "\n";
        }

        echo "\n";
    } else {
        echo "MediaItem {$id}: Not found\n\n";
    }
}

// Check total counts
$totalMediaItems = MediaItem::count();
$totalWithMediaFiles = MediaItem::has('mediaFiles')->count();
$totalWithoutMediaFiles = $totalMediaItems - $totalWithMediaFiles;

echo "Summary:\n";
echo "  - Total MediaItem records: {$totalMediaItems}\n";
echo "  - MediaItems with Spatie Media: {$totalWithMediaFiles}\n";
echo "  - MediaItems without Spatie Media: {$totalWithoutMediaFiles}\n";

echo "\nVerification completed!\n";