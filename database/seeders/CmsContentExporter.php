<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use App\Models\CmsMenuItem;
use App\Models\MediaItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CmsContentExporter extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📤 Exporting existing CMS content...');

        $exportData = [
            'pages' => $this->exportPages(),
            'menu_items' => $this->exportMenuItems(),
            'media_items' => $this->exportMediaItems(),
            'exported_at' => now()->toISOString(),
            'total_pages' => CmsPage::count(),
            'total_menu_items' => CmsMenuItem::count(),
            'total_media_items' => MediaItem::count(),
        ];

        // Save to a file for later use
        $filename = 'cms_content_export_' . date('Y_m_d_H_i_s') . '.php';
        $filepath = database_path('exports/' . $filename);

        // Ensure exports directory exists
        File::ensureDirectoryExists(database_path('exports'));

        // Export as PHP array
        $phpContent = '<?php' . PHP_EOL . PHP_EOL;
        $phpContent .= 'return ' . var_export($exportData, true) . ';' . PHP_EOL;

        File::put($filepath, $phpContent);

        $this->command->info('✅ CMS content exported successfully!');
        $this->command->info("📁 Export file: {$filepath}");
        $this->command->info('');
        $this->command->info('📊 Export Summary:');
        $this->command->info("   • {$exportData['total_pages']} CMS Pages");
        $this->command->info("   • {$exportData['total_menu_items']} Menu Items");
        $this->command->info("   • {$exportData['total_media_items']} Media Items");
        $this->command->info('');
        $this->command->info('💡 Next steps:');
        $this->command->info('   1. Copy the export file to your new project');
        $this->command->info('   2. Run: php artisan db:seed --class=CmsContentImporter');
    }

    private function exportPages(): array
    {
        $pages = CmsPage::all();

        $exportedPages = [];
        foreach ($pages as $page) {
            $exportedPages[] = [
                'title' => $page->title,
                'slug' => $page->slug,
                'status' => $page->status->value,
                'published_at' => $page->published_at?->toISOString(),
                'description' => $page->description,
                'keywords' => $page->keywords,
                'content' => $page->content,
            ];
        }

        return $exportedPages;
    }

    private function exportMenuItems(): array
    {
        $menuItems = CmsMenuItem::with(['reference'])->get();

        $exportedMenuItems = [];
        foreach ($menuItems as $item) {
            $exportedMenuItems[] = [
                'type' => $item->type->value,
                'title' => $item->title,
                'url' => $item->url,
                'position' => $item->position,
                'icon' => $item->icon,
                'icon_svg' => $item->icon_svg,
                'parent_id' => $item->parent_id,
                'reference_type' => $item->reference_type,
                'reference_id' => $item->reference_id,
                'dropdown_page_id' => $item->dropdown_page_id,
            ];
        }

        return $exportedMenuItems;
    }

    private function exportMediaItems(): array
    {
        $mediaItems = MediaItem::all();

        $exportedMediaItems = [];
        foreach ($mediaItems as $item) {
            $exportedMediaItems[] = [
                'name' => $item->name,
                'alt' => $item->alt,
                'description' => $item->description,
                'type' => $item->type,
                'collection' => $item->collection,
                'is_public' => $item->is_public,
                'files' => $item->files,
                'tags' => $item->tags,
            ];
        }

        return $exportedMediaItems;
    }
}