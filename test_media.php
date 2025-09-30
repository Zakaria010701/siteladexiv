<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$media = App\Models\Media::find(21);
var_dump(method_exists($media, 'addMediaFromPath'));
var_dump(method_exists($media, 'getMedia'));
var_dump(method_exists($media, 'addMedia'));