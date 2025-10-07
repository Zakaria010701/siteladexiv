<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Checking media table...\n";

    $tables = DB::select("SHOW TABLES LIKE 'media_items'");
    if (count($tables) > 0) {
        echo "Media items table exists\n";

        $columns = DB::select('DESCRIBE media_items');
        echo "Columns in media_items table:\n";
        foreach ($columns as $column) {
            echo "- {$column->Field} ({$column->Type})\n";
        }
    } else {
        echo "Media items table does not exist\n";

        // Also check if the old media table exists
        $oldTables = DB::select("SHOW TABLES LIKE 'media'");
        if (count($oldTables) > 0) {
            echo "Old media table (Spatie) exists\n";
        }
    }

    // Check actual data in media_items table
    echo "\nChecking media_items data...\n";
    $mediaItems = DB::select('SELECT id, name, files, collection FROM media_items LIMIT 5');
    foreach ($mediaItems as $item) {
        echo "MediaItem {$item->id}: {$item->name}\n";
        echo "  Collection: {$item->collection}\n";
        echo "  Files: " . ($item->files ? $item->files : 'NULL') . "\n";
    }

    // Check Spatie media table
    echo "\nChecking Spatie media table...\n";
    $spatieMedia = DB::select('SELECT id, model_id, model_type, file_name, collection_name FROM media LIMIT 10');
    foreach ($spatieMedia as $media) {
        echo "Media {$media->id}: {$media->file_name} (Model: {$media->model_type} #{$media->model_id})\n";
    }

    // Check which MediaItem records have Spatie Media associations
    echo "\nChecking MediaItem-Spatie Media relationships...\n";
    $mediaItemsWithMedia = DB::select("
        SELECT mi.id, mi.name, COUNT(m.id) as media_count
        FROM media_items mi
        LEFT JOIN media m ON m.model_id = mi.id AND m.model_type = 'App\\\\Models\\\\MediaItem'
        GROUP BY mi.id, mi.name
        ORDER BY media_count DESC
    ");

    foreach ($mediaItemsWithMedia as $item) {
        echo "MediaItem {$item->id}: {$item->name} ({$item->media_count} media files)\n";
    }

    // Create a fix script for MediaItem records without Spatie Media associations
    echo "\nCreating fix script for missing Spatie Media associations...\n";

    // Find MediaItem records that have files but no Spatie Media
    $itemsNeedingMedia = DB::select("
        SELECT mi.id, mi.name, mi.files, mi.collection
        FROM media_items mi
        LEFT JOIN media m ON m.model_id = mi.id AND m.model_type = 'App\\\\Models\\\\MediaItem'
        WHERE m.id IS NULL AND mi.files IS NOT NULL AND mi.files != '[]'
        GROUP BY mi.id, mi.name, mi.files, mi.collection
    ");

    echo "Found " . count($itemsNeedingMedia) . " MediaItem records that need Spatie Media associations:\n";
    foreach ($itemsNeedingMedia as $item) {
        echo "MediaItem {$item->id}: {$item->name}\n";
        echo "  Files: {$item->files}\n";
        echo "  Collection: {$item->collection}\n";
    }

    // Create a simple fix script
    echo "\nGenerating fix script...\n";

    $fixScriptContent = <<<PHP
<?php

require_once 'vendor/autoload.php';

\$app = require_once 'bootstrap/app.php';
\$app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap();

use App\\Models\\MediaItem;
use Illuminate\\Support\\Facades\\Log;

echo "Starting to fix missing Spatie Media associations...\\n";

// Fix specific MediaItem records that need Spatie Media associations
\$itemsToFix = [
PHP;

    foreach ($itemsNeedingMedia as $item) {
        $files = json_decode($item->files);
        if (!empty($files)) {
            foreach ($files as $filePath) {
                $normalizedPath = str_replace('\\', '/', $filePath);
                $fixScriptContent .= "    // MediaItem {$item->id}: {$item->name}\n";
                $fixScriptContent .= "    \$mediaItem = MediaItem::find({$item->id});\n";
                $fixScriptContent .= "    if (\$mediaItem) {\n";
                $fixScriptContent .= "        \$filePath = '{$normalizedPath}';\n";
                $fixScriptContent .= "        \$fullPath = storage_path('app/public/' . \$filePath);\n";
                $fixScriptContent .= "        \n";
                $fixScriptContent .= "        if (file_exists(\$fullPath)) {\n";
                $fixScriptContent .= "            echo \"Creating Spatie Media for MediaItem {$item->id}: \$filePath\\n\";\n";
                $fixScriptContent .= "            \n";
                $fixScriptContent .= "            try {\n";
                $fixScriptContent .= "                \$mediaItem->clearMediaCollection('{$item->collection}');\n";
                $fixScriptContent .= "                \$mediaFile = \$mediaItem->addMediaFromPath(\$fullPath)\n";
                $fixScriptContent .= "                    ->usingName(\$mediaItem->name)\n";
                $fixScriptContent .= "                    ->usingFileName(basename(\$fullPath))\n";
                $fixScriptContent .= "                    ->toMediaCollection('{$item->collection}');\n";
                $fixScriptContent .= "                    \n";
                $fixScriptContent .= "                Log::info('Created Spatie Media: ' . \$mediaFile->id . ' for MediaItem {$item->id}');\n";
                $fixScriptContent .= "            } catch (Exception \$e) {\n";
                $fixScriptContent .= "                Log::error('Failed to create Spatie Media for MediaItem {$item->id}: ' . \$e->getMessage());\n";
                $fixScriptContent .= "            }\n";
                $fixScriptContent .= "        }\n";
                $fixScriptContent .= "    }\n";
            }
        }
    }

    $fixScriptContent .= <<<PHP
];

echo "Fix script completed!\\n";
PHP;

    file_put_contents('fix_media_associations.php', $fixScriptContent);
    echo "Fix script generated: fix_media_associations.php\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}