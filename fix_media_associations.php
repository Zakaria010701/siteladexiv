<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MediaItem;
use Illuminate\Support\Facades\Log;

echo "Starting to fix missing Spatie Media associations...\n";

// Fix specific MediaItem records that need Spatie Media associations

// MediaItem 3: 01K6AY3JV2CEB3H352AEYR30KW Preview
$mediaItem = MediaItem::find(3);
if ($mediaItem) {
    $filePath = '1/conversions/01K6AY3JV2CEB3H352AEYR30KW-preview.jpg';
    $fullPath = storage_path('app/public/' . $filePath);

    if (file_exists($fullPath)) {
        echo "Creating Spatie Media for MediaItem 3: $filePath\n";

        try {
            $mediaItem->clearMediaCollection('storage');
            $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                ->usingName($mediaItem->name)
                ->usingFileName(basename($fullPath))
                ->toMediaCollection('storage');

            Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 3');
        } catch (Exception $e) {
            Log::error('Failed to create Spatie Media for MediaItem 3: ' . $e->getMessage());
        }
    }
}

// MediaItem 7: 01K6AK852M08576908WA7943B7 Thumb
$mediaItem = MediaItem::find(7);
if ($mediaItem) {
    $filePath = '15/conversions/01K6AK852M08576908WA7943B7-thumb.jpg';
    $fullPath = storage_path('app/public/' . $filePath);

    if (file_exists($fullPath)) {
        echo "Creating Spatie Media for MediaItem 7: $filePath\n";

        try {
            $mediaItem->clearMediaCollection('storage');
            $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                ->usingName($mediaItem->name)
                ->usingFileName(basename($fullPath))
                ->toMediaCollection('storage');

            Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 7');
        } catch (Exception $e) {
            Log::error('Failed to create Spatie Media for MediaItem 7: ' . $e->getMessage());
        }
    }
}

// MediaItem 8: 01K6AK86BK5Z2XYD1PJHMQBFR1
$mediaItem = MediaItem::find(8);
if ($mediaItem) {
    $filePath = '16/01K6AK86BK5Z2XYD1PJHMQBFR1.webp';
    $fullPath = storage_path('app/public/' . $filePath);

    if (file_exists($fullPath)) {
        echo "Creating Spatie Media for MediaItem 8: $filePath\n";

        try {
            $mediaItem->clearMediaCollection('storage');
            $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                ->usingName($mediaItem->name)
                ->usingFileName(basename($fullPath))
                ->toMediaCollection('storage');

            Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 8');
        } catch (Exception $e) {
            Log::error('Failed to create Spatie Media for MediaItem 8: ' . $e->getMessage());
        }
    }
}

echo "Fix script completed!\n";
    $mediaItem = MediaItem::find(3);
    if ($mediaItem) {
        $filePath = '1/conversions/01K6AY3JV2CEB3H352AEYR30KW-preview.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 3: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 3');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 3: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 7: 01K6AK852M08576908WA7943B7 Thumb
    $mediaItem = MediaItem::find(7);
    if ($mediaItem) {
        $filePath = '15/conversions/01K6AK852M08576908WA7943B7-thumb.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 7: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 7');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 7: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 8: 01K6AK86BK5Z2XYD1PJHMQBFR1
    $mediaItem = MediaItem::find(8);
    if ($mediaItem) {
        $filePath = '16/01K6AK86BK5Z2XYD1PJHMQBFR1.webp';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 8: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 8');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 8: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 9: 01K6AK86BK5Z2XYD1PJHMQBFR1 Preview
    $mediaItem = MediaItem::find(9);
    if ($mediaItem) {
        $filePath = '16/conversions/01K6AK86BK5Z2XYD1PJHMQBFR1-preview.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 9: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 9');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 9: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 10: 01K6AK86BK5Z2XYD1PJHMQBFR1 Thumb
    $mediaItem = MediaItem::find(10);
    if ($mediaItem) {
        $filePath = '16/conversions/01K6AK86BK5Z2XYD1PJHMQBFR1-thumb.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 10: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 10');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 10: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 11: 01K6AK87PJDGMGY3YHN3GEWC62
    $mediaItem = MediaItem::find(11);
    if ($mediaItem) {
        $filePath = '17/01K6AK87PJDGMGY3YHN3GEWC62.webp';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 11: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 11');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 11: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 12: 01K6AK87PJDGMGY3YHN3GEWC62 Preview
    $mediaItem = MediaItem::find(12);
    if ($mediaItem) {
        $filePath = '17/conversions/01K6AK87PJDGMGY3YHN3GEWC62-preview.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 12: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 12');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 12: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 13: 01K6AK87PJDGMGY3YHN3GEWC62 Thumb
    $mediaItem = MediaItem::find(13);
    if ($mediaItem) {
        $filePath = '17/conversions/01K6AK87PJDGMGY3YHN3GEWC62-thumb.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 13: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 13');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 13: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 14: 01K6AK89115JX7Z64VGYS9VWKZ
    $mediaItem = MediaItem::find(14);
    if ($mediaItem) {
        $filePath = '18/01K6AK89115JX7Z64VGYS9VWKZ.webp';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 14: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 14');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 14: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 15: 01K6AK89115JX7Z64VGYS9VWKZ Preview
    $mediaItem = MediaItem::find(15);
    if ($mediaItem) {
        $filePath = '18/conversions/01K6AK89115JX7Z64VGYS9VWKZ-preview.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 15: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 15');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 15: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 16: 01K6AK89115JX7Z64VGYS9VWKZ Thumb
    $mediaItem = MediaItem::find(16);
    if ($mediaItem) {
        $filePath = '18/conversions/01K6AK89115JX7Z64VGYS9VWKZ-thumb.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 16: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 16');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 16: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 17: 01K6AKCQ1BCBNKMK016BXT2J7H
    $mediaItem = MediaItem::find(17);
    if ($mediaItem) {
        $filePath = '19/01K6AKCQ1BCBNKMK016BXT2J7H.webp';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 17: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 17');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 17: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 18: 01K6AKCQ1BCBNKMK016BXT2J7H Preview
    $mediaItem = MediaItem::find(18);
    if ($mediaItem) {
        $filePath = '19/conversions/01K6AKCQ1BCBNKMK016BXT2J7H-preview.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 18: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 18');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 18: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 19: 01K6AKCQ1BCBNKMK016BXT2J7H Thumb
    $mediaItem = MediaItem::find(19);
    if ($mediaItem) {
        $filePath = '19/conversions/01K6AKCQ1BCBNKMK016BXT2J7H-thumb.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 19: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 19');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 19: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 20: 01K6AY3M3NW50VASSAS8RJZVAN
    $mediaItem = MediaItem::find(20);
    if ($mediaItem) {
        $filePath = '2/01K6AY3M3NW50VASSAS8RJZVAN.webp';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 20: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 20');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 20: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 21: 01K6AY3M3NW50VASSAS8RJZVAN Preview
    $mediaItem = MediaItem::find(21);
    if ($mediaItem) {
        $filePath = '2/conversions/01K6AY3M3NW50VASSAS8RJZVAN-preview.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 21: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 21');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 21: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 22: 01K6AY3M3NW50VASSAS8RJZVAN Thumb
    $mediaItem = MediaItem::find(22);
    if ($mediaItem) {
        $filePath = '2/conversions/01K6AY3M3NW50VASSAS8RJZVAN-thumb.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 22: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 22');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 22: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 23: 01K6APCKTGQHQ4ZPEV6EDBAPF9
    $mediaItem = MediaItem::find(23);
    if ($mediaItem) {
        $filePath = '20/01K6APCKTGQHQ4ZPEV6EDBAPF9.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 23: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 23');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 23: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 24: 01K6APCKTGQHQ4ZPEV6EDBAPF9 Preview
    $mediaItem = MediaItem::find(24);
    if ($mediaItem) {
        $filePath = '20/conversions/01K6APCKTGQHQ4ZPEV6EDBAPF9-preview.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 24: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 24');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 24: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 25: 01K6APCKTGQHQ4ZPEV6EDBAPF9 Thumb
    $mediaItem = MediaItem::find(25);
    if ($mediaItem) {
        $filePath = '20/conversions/01K6APCKTGQHQ4ZPEV6EDBAPF9-thumb.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 25: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 25');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 25: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 26: 01K6APCMT224VR3T60KCTAFXPP
    $mediaItem = MediaItem::find(26);
    if ($mediaItem) {
        $filePath = '21/01K6APCMT224VR3T60KCTAFXPP.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 26: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 26');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 26: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 27: 01K6APCMT224VR3T60KCTAFXPP Preview
    $mediaItem = MediaItem::find(27);
    if ($mediaItem) {
        $filePath = '21/conversions/01K6APCMT224VR3T60KCTAFXPP-preview.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 27: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 27');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 27: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 28: 01K6APCMT224VR3T60KCTAFXPP Thumb
    $mediaItem = MediaItem::find(28);
    if ($mediaItem) {
        $filePath = '21/conversions/01K6APCMT224VR3T60KCTAFXPP-thumb.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 28: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 28');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 28: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 29: 01K6APCNZSDTT7E2B6THWAH9SY
    $mediaItem = MediaItem::find(29);
    if ($mediaItem) {
        $filePath = '22/01K6APCNZSDTT7E2B6THWAH9SY.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 29: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 29');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 29: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 30: 01K6APCNZSDTT7E2B6THWAH9SY Preview
    $mediaItem = MediaItem::find(30);
    if ($mediaItem) {
        $filePath = '22/conversions/01K6APCNZSDTT7E2B6THWAH9SY-preview.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 30: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 30');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 30: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 31: 01K6APCNZSDTT7E2B6THWAH9SY Thumb
    $mediaItem = MediaItem::find(31);
    if ($mediaItem) {
        $filePath = '22/conversions/01K6APCNZSDTT7E2B6THWAH9SY-thumb.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 31: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 31');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 31: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 32: 01K6APCPVXJ2H86N4971K3425X
    $mediaItem = MediaItem::find(32);
    if ($mediaItem) {
        $filePath = '23/01K6APCPVXJ2H86N4971K3425X.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 32: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 32');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 32: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 33: 01K6APCPVXJ2H86N4971K3425X Preview
    $mediaItem = MediaItem::find(33);
    if ($mediaItem) {
        $filePath = '23/conversions/01K6APCPVXJ2H86N4971K3425X-preview.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 33: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 33');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 33: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 34: 01K6APCPVXJ2H86N4971K3425X Thumb
    $mediaItem = MediaItem::find(34);
    if ($mediaItem) {
        $filePath = '23/conversions/01K6APCPVXJ2H86N4971K3425X-thumb.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 34: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 34');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 34: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 35: 01K6AY3NBSAXKYE5GNHTPTCRXP
    $mediaItem = MediaItem::find(35);
    if ($mediaItem) {
        $filePath = '3/01K6AY3NBSAXKYE5GNHTPTCRXP.webp';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 35: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 35');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 35: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 36: 01K6AY3NBSAXKYE5GNHTPTCRXP Preview
    $mediaItem = MediaItem::find(36);
    if ($mediaItem) {
        $filePath = '3/conversions/01K6AY3NBSAXKYE5GNHTPTCRXP-preview.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 36: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 36');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 36: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 37: 01K6AY3NBSAXKYE5GNHTPTCRXP Thumb
    $mediaItem = MediaItem::find(37);
    if ($mediaItem) {
        $filePath = '3/conversions/01K6AY3NBSAXKYE5GNHTPTCRXP-thumb.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 37: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 37');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 37: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 38: 01K6AY3PKW962F3ZQC86B5JXSN
    $mediaItem = MediaItem::find(38);
    if ($mediaItem) {
        $filePath = '4/01K6AY3PKW962F3ZQC86B5JXSN.webp';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 38: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 38');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 38: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 39: 01K6AY3PKW962F3ZQC86B5JXSN Preview
    $mediaItem = MediaItem::find(39);
    if ($mediaItem) {
        $filePath = '4/conversions/01K6AY3PKW962F3ZQC86B5JXSN-preview.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 39: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 39');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 39: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 40: 01K6AY3PKW962F3ZQC86B5JXSN Thumb
    $mediaItem = MediaItem::find(40);
    if ($mediaItem) {
        $filePath = '4/conversions/01K6AY3PKW962F3ZQC86B5JXSN-thumb.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 40: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 40');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 40: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 41: 01K6CX1ZHBVAJKY3790BJZYMA8
    $mediaItem = MediaItem::find(41);
    if ($mediaItem) {
        $filePath = '5/01K6CX1ZHBVAJKY3790BJZYMA8.png';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 41: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 41');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 41: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 42: 01K6CX1ZHBVAJKY3790BJZYMA8 Preview
    $mediaItem = MediaItem::find(42);
    if ($mediaItem) {
        $filePath = '5/conversions/01K6CX1ZHBVAJKY3790BJZYMA8-preview.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 42: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 42');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 42: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 43: 01K6CX1ZHBVAJKY3790BJZYMA8 Thumb
    $mediaItem = MediaItem::find(43);
    if ($mediaItem) {
        $filePath = '5/conversions/01K6CX1ZHBVAJKY3790BJZYMA8-thumb.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 43: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 43');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 43: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 44: 01K6CX2TKYY8Q6D5MPG25HDT8K
    $mediaItem = MediaItem::find(44);
    if ($mediaItem) {
        $filePath = '6/01K6CX2TKYY8Q6D5MPG25HDT8K.png';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 44: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 44');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 44: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 45: 01K6CX2TKYY8Q6D5MPG25HDT8K Preview
    $mediaItem = MediaItem::find(45);
    if ($mediaItem) {
        $filePath = '6/conversions/01K6CX2TKYY8Q6D5MPG25HDT8K-preview.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 45: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 45');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 45: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 46: 01K6CX2TKYY8Q6D5MPG25HDT8K Thumb
    $mediaItem = MediaItem::find(46);
    if ($mediaItem) {
        $filePath = '6/conversions/01K6CX2TKYY8Q6D5MPG25HDT8K-thumb.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 46: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 46');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 46: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 47: 01K6CX3V1GRV90G33QZCAHE2ZZ
    $mediaItem = MediaItem::find(47);
    if ($mediaItem) {
        $filePath = '7/01K6CX3V1GRV90G33QZCAHE2ZZ.png';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 47: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 47');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 47: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 48: 01K6CX3V1GRV90G33QZCAHE2ZZ Preview
    $mediaItem = MediaItem::find(48);
    if ($mediaItem) {
        $filePath = '7/conversions/01K6CX3V1GRV90G33QZCAHE2ZZ-preview.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 48: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 48');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 48: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 49: 01K6CX3V1GRV90G33QZCAHE2ZZ Thumb
    $mediaItem = MediaItem::find(49);
    if ($mediaItem) {
        $filePath = '7/conversions/01K6CX3V1GRV90G33QZCAHE2ZZ-thumb.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 49: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 49');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 49: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 50: 01K6CX4F3M734XNASPKAR377XH
    $mediaItem = MediaItem::find(50);
    if ($mediaItem) {
        $filePath = '8/01K6CX4F3M734XNASPKAR377XH.png';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 50: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 50');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 50: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 51: 01K6CX4F3M734XNASPKAR377XH Preview
    $mediaItem = MediaItem::find(51);
    if ($mediaItem) {
        $filePath = '8/conversions/01K6CX4F3M734XNASPKAR377XH-preview.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 51: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 51');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 51: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 52: 01K6CX4F3M734XNASPKAR377XH Thumb
    $mediaItem = MediaItem::find(52);
    if ($mediaItem) {
        $filePath = '8/conversions/01K6CX4F3M734XNASPKAR377XH-thumb.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 52: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 52');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 52: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 53: 01K5HM4PSH0YTZ93PNEVG138WK
    $mediaItem = MediaItem::find(53);
    if ($mediaItem) {
        $filePath = 'slider-images/01K5HM4PSH0YTZ93PNEVG138WK.webp';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 53: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 53');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 53: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 54: 01K5HM4PSH0YTZ93PNEVG138WM
    $mediaItem = MediaItem::find(54);
    if ($mediaItem) {
        $filePath = 'slider-images/01K5HM4PSH0YTZ93PNEVG138WM.webp';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 54: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 54');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 54: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 55: 01K5HM4PSV1M51DFAQA2V0A88F
    $mediaItem = MediaItem::find(55);
    if ($mediaItem) {
        $filePath = 'slider-images/01K5HM4PSV1M51DFAQA2V0A88F.webp';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 55: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 55');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 55: ' . $e->getMessage());
            }
        }
    }
    // MediaItem 56: 01K5HM4PSV1M51DFAQA2V0A88G
    $mediaItem = MediaItem::find(56);
    if ($mediaItem) {
        $filePath = 'slider-images/01K5HM4PSV1M51DFAQA2V0A88G.webp';
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (file_exists($fullPath)) {
            echo "Creating Spatie Media for MediaItem 56: $filePath\n";
            
            try {
                $mediaItem->clearMediaCollection('storage');
                $mediaFile = $mediaItem->addMediaFromPath($fullPath)
                    ->usingName($mediaItem->name)
                    ->usingFileName(basename($fullPath))
                    ->toMediaCollection('storage');
                    
                Log::info('Created Spatie Media: ' . $mediaFile->id . ' for MediaItem 56');
            } catch (Exception $e) {
                Log::error('Failed to create Spatie Media for MediaItem 56: ' . $e->getMessage());
            }
        }
    }

echo "Fix script completed!\n";