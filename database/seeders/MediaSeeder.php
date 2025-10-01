<?php

namespace Database\Seeders;

use App\Models\MediaItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔍 Scanning for media files to seed...');

        // Define directories to scan for media files
        $directoriesToScan = [
            'public/images' => 'images',
            'storage/app/public' => 'storage',
            'public/storage' => 'public_storage',
        ];

        $mediaFound = 0;
        $mediaCreated = 0;

        foreach ($directoriesToScan as $directory => $collection) {
            if (!is_dir($directory)) {
                $this->command->warn("Directory not found: {$directory}");
                continue;
            }

            $this->command->info("📂 Scanning: {$directory}");

            // Find all image files
            $files = $this->scanForMediaFiles($directory);

            foreach ($files as $file) {
                $mediaFound++;

                // Get relative path for database storage
                $relativePath = $this->getRelativePath($file, $directory);

                // Generate a name from the filename
                $name = $this->generateNameFromFile($file);

                // Check if media already exists for this file
                $existingMedia = MediaItem::where('name', $name)
                    ->orWhere('files', 'like', "%{$relativePath}%")
                    ->first();

                if ($existingMedia) {
                    $this->command->warn("⚠️  Media already exists for: {$name}");

                    // Check if the existing media has actual files attached via Spatie Media Library
                    $attachedMediaCount = $existingMedia->mediaFiles()->count();
                    if ($attachedMediaCount == 0) {
                        // Media exists but no files attached, attach the file
                        try {
                            $existingMedia
                                ->addMediaFromPath($file)
                                ->usingName($name)
                                ->usingFileName(basename($file))
                                ->toMediaCollection($existingMedia->collection ?: 'default');

                            $this->command->info("🔗 Attached file to existing media: {$name}");
                            $mediaCreated++;
                        } catch (\Exception $e) {
                            $this->command->error("❌ Failed to attach file {$file} to existing media {$name}: " . $e->getMessage());
                        }
                    }
                    continue;
                }

                // Create new media record and attach file
                try {
                    $mediaItem = MediaItem::create([
                        'name' => $name,
                        'alt' => $name,
                        'description' => "Auto-imported media file: {$name}",
                        'type' => $this->getMediaType($file),
                        'collection' => $collection,
                        'is_public' => true,
                        'files' => [$relativePath],
                    ]);

                    // Attach the actual file using Spatie Media Library
                    $mediaItem
                        ->addMediaFromPath($file)
                        ->usingName($name)
                        ->usingFileName(basename($file))
                        ->toMediaCollection($collection);

                    $mediaCreated++;
                    $this->command->info("✅ Created media with file: {$name} ({$relativePath})");

                } catch (\Exception $e) {
                    $this->command->error("❌ Failed to create media for {$file}: " . $e->getMessage());
                }
            }
        }

        $this->command->info("📊 Scan complete!");
        $this->command->info("📁 Media files found: {$mediaFound}");
        $this->command->info("✅ Media records created: {$mediaCreated}");

        if ($mediaFound === 0) {
            $this->command->warn("⚠️  No media files found in scanned directories.");
            $this->command->info("💡 To use this seeder:");
            $this->command->info("1. Place your images in: public/images/ or storage/app/public/");
            $this->command->info("2. Run: php artisan db:seed --class=MediaSeeder");
        }
    }

    /**
     * Scan directory for media files
     */
    private function scanForMediaFiles(string $directory): array
    {
        $files = [];

        if (!is_dir($directory)) {
            return $files;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $this->isMediaFile($file->getPathname())) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Check if file is a media file
     */
    private function isMediaFile(string $filePath): bool
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        return in_array($extension, $allowedExtensions);
    }

    /**
     * Get relative path for database storage
     */
    private function getRelativePath(string $fullPath, string $baseDirectory): string
    {
        // Remove the base directory from the path
        $relativePath = str_replace($baseDirectory . DIRECTORY_SEPARATOR, '', $fullPath);

        // For storage files, we want the path after 'app/public/'
        if (str_contains($relativePath, 'app/public/')) {
            $relativePath = str_replace('app/public/', '', $relativePath);
        }

        return $relativePath;
    }

    /**
     * Generate a readable name from filename
     */
    private function generateNameFromFile(string $filePath): string
    {
        $filename = pathinfo($filePath, PATHINFO_FILENAME);
        $name = str_replace(['-', '_'], ' ', $filename);
        $name = ucwords($name);
        return $name;
    }

    /**
     * Get media type based on file extension
     */
    private function getMediaType(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        return match ($extension) {
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp' => 'image',
            'svg' => 'icon',
            default => 'image',
        };
    }
}