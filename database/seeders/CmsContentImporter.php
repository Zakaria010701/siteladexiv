<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use App\Models\CmsMenuItem;
use App\Models\MediaItem;
use App\Enums\Cms\CmsPageStatus;
use App\Enums\Cms\CmsMenuItemType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CmsContentImporter extends Seeder
{
    private array $importedPages = [];
    private array $importedMediaItems = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📥 Importing CMS content...');

        // Check if export file exists
        $exportFile = $this->findLatestExportFile();

        if (!$exportFile) {
            $this->command->error('❌ No export file found!');
            $this->command->info('💡 First run: php artisan db:seed --class=CmsContentExporter');
            return;
        }

        $exportData = include $exportFile;

        $this->command->info("📁 Using export file: " . basename($exportFile));
        $this->command->info("📅 Exported on: {$exportData['exported_at']}");
        $this->command->info('');

        // Import in correct order: Media -> Pages -> Menu Items
        $this->importMediaItems($exportData['media_items']);
        $this->importPages($exportData['pages']);
        $this->importMenuItems($exportData['menu_items']);

        $this->command->info('');
        $this->command->info('✅ CMS content imported successfully!');
        $this->command->info('');
        $this->command->info('📊 Import Summary:');
        $this->command->info("   • {$exportData['total_pages']} CMS Pages imported");
        $this->command->info("   • {$exportData['total_menu_items']} Menu Items imported");
        $this->command->info("   • {$exportData['total_media_items']} Media Items imported");
    }

    private function importMediaItems(array $mediaItems): void
    {
        $this->command->info('🖼️  Importing Media Items...');

        foreach ($mediaItems as $itemData) {
            try {
                $mediaItem = MediaItem::create([
                    'name' => $itemData['name'],
                    'alt' => $itemData['alt'],
                    'description' => $itemData['description'],
                    'type' => $itemData['type'],
                    'collection' => $itemData['collection'],
                    'is_public' => $itemData['is_public'],
                    'files' => $itemData['files'],
                    'tags' => $itemData['tags'],
                ]);

                $this->importedMediaItems[$mediaItem->name] = $mediaItem;
                $this->command->info("   ✅ {$mediaItem->name}");

            } catch (\Exception $e) {
                $this->command->error("   ❌ Failed to import {$itemData['name']}: " . $e->getMessage());
            }
        }
    }

    private function importPages(array $pages): void
    {
        $this->command->info('📄 Importing CMS Pages...');

        foreach ($pages as $pageData) {
            try {
                $page = CmsPage::create([
                    'title' => $pageData['title'],
                    'slug' => $pageData['slug'],
                    'status' => CmsPageStatus::from($pageData['status']),
                    'published_at' => $pageData['published_at'] ? \Carbon\Carbon::parse($pageData['published_at']) : null,
                    'description' => $pageData['description'],
                    'keywords' => $pageData['keywords'],
                    'content' => $pageData['content'],
                ]);

                $this->importedPages[$page->slug] = $page;
                $this->command->info("   ✅ {$page->title} ({$page->slug})");

            } catch (\Exception $e) {
                $this->command->error("   ❌ Failed to import {$pageData['title']}: " . $e->getMessage());
            }
        }
    }

    private function importMenuItems(array $menuItems): void
    {
        $this->command->info('📋 Importing Menu Items...');

        foreach ($menuItems as $itemData) {
            try {
                // Handle reference relationships
                $referenceType = $itemData['reference_type'];
                $referenceId = $itemData['reference_id'];

                // If it's a page reference, find the imported page
                if ($referenceType === 'App\Models\CmsPage' && $referenceId) {
                    $originalPage = CmsPage::find($referenceId);
                    if ($originalPage && isset($this->importedPages[$originalPage->slug])) {
                        $referenceId = $this->importedPages[$originalPage->slug]->id;
                    }
                }

                $menuItem = CmsMenuItem::create([
                    'type' => CmsMenuItemType::from($itemData['type']),
                    'title' => $itemData['title'],
                    'url' => $itemData['url'],
                    'position' => $itemData['position'],
                    'icon' => $itemData['icon'],
                    'icon_svg' => $itemData['icon_svg'],
                    'parent_id' => $itemData['parent_id'],
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'dropdown_page_id' => $itemData['dropdown_page_id'],
                ]);

                $this->command->info("   ✅ {$menuItem->title} ({$menuItem->type->value})");

            } catch (\Exception $e) {
                $this->command->error("   ❌ Failed to import {$itemData['title']}: " . $e->getMessage());
            }
        }
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