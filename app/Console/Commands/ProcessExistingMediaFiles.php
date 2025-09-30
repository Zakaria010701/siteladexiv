<?php

namespace App\Console\Commands;

use App\Models\MediaItem;
use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

class ProcessExistingMediaFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-existing-media-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process existing media files and add them to Spatie Media Library';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing existing media files and syncing to Spatie Media Library...');

        // First, clean up any orphaned records
        $mediaRecords = MediaItem::all();
        $deleted = 0;

        foreach ($mediaRecords as $media) {
            // Check if this media record has any actual files in the Spatie Media Library
            $spatieMediaFiles = SpatieMedia::where('model_type', 'App\\Models\\MediaItem')
                ->where('model_id', $media->id)
                ->count();

            // If no files in Spatie Media Library, delete the record
            if ($spatieMediaFiles === 0) {
                $media->delete();
                $deleted++;
                $this->line("Deleted media record: {$media->name} (no files found)");
            }
        }

        $this->info("Cleanup complete! Records deleted: {$deleted}");

        // Now sync existing files from media-gallery directory
        $this->info('Syncing existing files from media-gallery to Spatie Media Library...');

        $files = glob(storage_path('app/public/media-gallery/*.png'));
        $synced = 0;

        foreach ($files as $filePath) {
            $filename = basename($filePath);
            $this->line("Processing: {$filename}");

            // Check if this file is already in Spatie Media Library
            $existing = SpatieMedia::where('file_name', $filename)->count();

            if ($existing > 0) {
                $this->line("  ✓ Already exists in Spatie Media Library");
                continue;
            }

            // Create a media record
            $media = new MediaItem();
            $media->name = 'Gallery Image: ' . $filename;
            $media->type = 'image';
            $media->collection = 'default';
            $media->is_public = true;
            $media->files = ['media-gallery/' . $filename];

            try {
                $media->save();
                $this->line("  ✓ Created media record ID: {$media->id}");

                // Add to Spatie Media Library
                $media->addMediaFromDisk('media-gallery/' . $filename, 'public')->toMediaCollection('default');
                $synced++;
                $this->line("  ✓ Added to Spatie Media Library");
            } catch (\Exception $e) {
                $this->line("  ✗ Error: " . $e->getMessage());
            }
        }

        $this->info("Sync complete!");
        $this->info("Files synced: {$synced}");
        $this->info("Total media records: " . MediaItem::count());
        $this->info("Total files in Spatie Media Library: " . SpatieMedia::count());

        if ($synced > 0) {
            $this->info('');
            $this->info('✅ Your All Media section should now show the uploaded images!');
            $this->info('Go to /cms/all-media to see them.');
        }

        return Command::SUCCESS;
    }
}
