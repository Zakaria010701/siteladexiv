<?php

namespace Database\Seeders;

use App\Models\CmsMenuItem;
use App\Models\CmsPage;
use App\Enums\Cms\CmsMenuItemType;
use Illuminate\Database\Seeder;

class CmsMenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📋 Seeding CMS Menu Items...');

        // Get existing pages for linking
        $pages = CmsPage::all();

        // Create main navigation menu structure
        $menuStructure = [
            [
                'type' => CmsMenuItemType::Link,
                'title' => 'Home',
                'url' => '/',
                'position' => 1,
                'icon' => null,
            ],
            [
                'type' => CmsMenuItemType::Page,
                'title' => 'About',
                'url' => null,
                'position' => 2,
                'icon' => null,
                'reference_type' => 'App\Models\CmsPage',
                'reference_id' => $pages->where('slug', 'about-us')->first()?->id,
            ],
            [
                'type' => CmsMenuItemType::Page,
                'title' => 'Services',
                'url' => null,
                'position' => 3,
                'icon' => null,
                'reference_type' => 'App\Models\CmsPage',
                'reference_id' => $pages->where('slug', 'services')->first()?->id,
            ],
            [
                'type' => CmsMenuItemType::Page,
                'title' => 'Contact',
                'url' => null,
                'position' => 4,
                'icon' => null,
                'reference_type' => 'App\Models\CmsPage',
                'reference_id' => $pages->where('slug', 'contact')->first()?->id,
            ],
        ];

        $created = 0;
        foreach ($menuStructure as $menuData) {
            $menuItem = CmsMenuItem::create([
                'type' => $menuData['type'],
                'title' => $menuData['title'],
                'url' => $menuData['url'],
                'position' => $menuData['position'],
                'icon' => $menuData['icon'],
                'reference_type' => $menuData['reference_type'] ?? null,
                'reference_id' => $menuData['reference_id'] ?? null,
            ]);

            $created++;
            $this->command->info("✅ Created menu item: {$menuItem->title}");
        }

        // Create footer menu items
        $footerMenuItems = [
            [
                'type' => CmsMenuItemType::Page,
                'title' => 'Privacy Policy',
                'url' => null,
                'position' => 1,
                'icon' => null,
                'reference_type' => 'App\Models\CmsPage',
                'reference_id' => $pages->where('slug', 'privacy-policy')->first()?->id,
            ],
            [
                'type' => CmsMenuItemType::Page,
                'title' => 'Terms of Service',
                'url' => null,
                'position' => 2,
                'icon' => null,
                'reference_type' => 'App\Models\CmsPage',
                'reference_id' => $pages->where('slug', 'terms-of-service')->first()?->id,
            ],
        ];

        foreach ($footerMenuItems as $menuData) {
            $menuItem = CmsMenuItem::create([
                'type' => $menuData['type'],
                'title' => $menuData['title'],
                'url' => $menuData['url'],
                'position' => $menuData['position'],
                'icon' => $menuData['icon'],
                'reference_type' => $menuData['reference_type'] ?? null,
                'reference_id' => $menuData['reference_id'] ?? null,
            ]);

            $created++;
            $this->command->info("✅ Created footer menu item: {$menuItem->title}");
        }

        // Create some dropdown menu items for Services
        $servicesPage = $pages->where('slug', 'services')->first();
        if ($servicesPage) {
            $dropdownItems = [
                [
                    'type' => CmsMenuItemType::Link,
                    'title' => 'Technical Services',
                    'url' => '/services#technical',
                    'position' => 1,
                    'icon' => null,
                ],
                [
                    'type' => CmsMenuItemType::Link,
                    'title' => 'Consulting',
                    'url' => '/services#consulting',
                    'position' => 2,
                    'icon' => null,
                ],
                [
                    'type' => CmsMenuItemType::Link,
                    'title' => 'Security Services',
                    'url' => '/services#security',
                    'position' => 3,
                    'icon' => null,
                ],
            ];

            foreach ($dropdownItems as $menuData) {
                $menuItem = CmsMenuItem::create([
                    'type' => $menuData['type'],
                    'title' => $menuData['title'],
                    'url' => $menuData['url'],
                    'position' => $menuData['position'],
                    'icon' => $menuData['icon'],
                    'parent_id' => null, // These will be linked to a dropdown parent
                ]);

                $created++;
                $this->command->info("✅ Created dropdown menu item: {$menuItem->title}");
            }
        }

        // Create a header contact item if HeaderContact model exists
        if (class_exists('\App\Models\HeaderContact')) {
            try {
                $headerContact = \App\Models\HeaderContact::first();
                if ($headerContact) {
                    $headerMenuItem = CmsMenuItem::create([
                        'type' => CmsMenuItemType::Header,
                        'title' => 'Contact Info',
                        'url' => null,
                        'position' => 99,
                        'icon' => null,
                        'reference_type' => 'App\Models\HeaderContact',
                        'reference_id' => $headerContact->id,
                    ]);

                    $created++;
                    $this->command->info("✅ Created header contact menu item");
                }
            } catch (\Exception $e) {
                $this->command->warn("⚠️  Could not create header contact menu item: " . $e->getMessage());
            }
        }

        $this->command->info("📊 Created {$created} CMS menu items");
    }
}