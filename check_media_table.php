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
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}