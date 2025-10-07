<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MediaItem;

echo "Testing MediaItem model...\n\n";

$mediaItem = MediaItem::find(3);

if ($mediaItem) {
    echo "MediaItem found: {$mediaItem->name}\n";
    echo "MediaItem class: " . get_class($mediaItem) . "\n";
    echo "Has InteractsWithMedia trait: " . (in_array('Spatie\MediaLibrary\InteractsWithMedia', class_uses($mediaItem)) ? 'Yes' : 'No') . "\n";
    echo "Implements HasMedia interface: " . (in_array('Spatie\MediaLibrary\HasMedia', class_implements($mediaItem)) ? 'Yes' : 'No') . "\n";

    // Check if method exists
    echo "Has addMediaFromPath method: " . (method_exists($mediaItem, 'addMediaFromPath') ? 'Yes' : 'No') . "\n";
    echo "Has clearMediaCollection method: " . (method_exists($mediaItem, 'clearMediaCollection') ? 'Yes' : 'No') . "\n";

    // Check available methods
    $methods = get_class_methods($mediaItem);
    $mediaMethods = array_filter($methods, function($method) {
        return strpos($method, 'Media') !== false || strpos($method, 'media') !== false;
    });

    echo "Media-related methods: " . implode(', ', $mediaMethods) . "\n";

    // Check if files exist
    if ($mediaItem->files && is_array($mediaItem->files)) {
        foreach ($mediaItem->files as $filePath) {
            $fullPath = storage_path('app/public/' . $filePath);
            echo "File path: {$filePath}\n";
            echo "Full path: {$fullPath}\n";
            echo "File exists: " . (file_exists($fullPath) ? 'Yes' : 'No') . "\n";
            if (file_exists($fullPath)) {
                echo "File size: " . filesize($fullPath) . " bytes\n";
                echo "MIME type: " . mime_content_type($fullPath) . "\n";
            }
            echo "\n";
        }
    }
} else {
    echo "MediaItem 3 not found\n";
}

echo "Test completed!\n";