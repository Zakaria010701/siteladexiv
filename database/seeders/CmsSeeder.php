<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting CMS seeding process...');
        $this->command->info('=====================================');

        // Check if we have existing content to export first
        $hasExistingContent = \App\Models\CmsPage::count() > 0;

        if ($hasExistingContent) {
            $this->command->info('📋 Existing CMS content detected!');
            $this->command->info('🔄 You can export your current content for backup/transfer:');
            $this->command->info('   php artisan db:seed --class=CmsContentExporter');
            $this->command->info('');
        }

        // For new installations, import from export file if available
        $exportFile = $this->findLatestExportFile();

        if ($exportFile) {
            $this->command->info('📥 Found existing export file - importing your CMS content...');
            $this->call(CmsContentImporter::class);
        } else {
            // Fallback to sample content for new installations
            $this->command->info('📄 No export file found - creating sample content...');
            $this->call(CmsPageSeeder::class);
            $this->call(CmsMenuItemSeeder::class);
            $this->call(MediaSeeder::class);

            $this->command->info('');
            $this->command->info('💡 To use your own content instead:');
            $this->command->info('   1. First export: php artisan db:seed --class=CmsContentExporter');
            $this->command->info('   2. Copy export file to new project');
            $this->command->info('   3. Then import: php artisan db:seed --class=CmsContentImporter');
        }

        $this->command->info('=====================================');
        $this->command->info('✅ CMS seeding completed successfully!');
    }

    private function findLatestExportFile(): ?string
    {
        $exportDir = database_path('exports');

        if (!is_dir($exportDir)) {
            return null;
        }

        $files = glob($exportDir . '/cms_content_export_*.php');

        if (empty($files)) {
            return null;
        }

        // Return the most recent file
        usort($files, function($a, $b) {
            return filemtime($b) <=> filemtime($a);
        });

        return $files[0];
    }
}