<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;
use App\Enums\Cms\CmsPageStatus;
use Carbon\Carbon;

class CmsPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📄 Seeding CMS Pages...');

        $pages = [
            [
                'title' => 'Home',
                'slug' => 'home',
                'status' => CmsPageStatus::Published,
                'published_at' => Carbon::now(),
                'description' => 'Welcome to our website - your premier destination for quality services',
                'keywords' => 'home, welcome, services, quality',
                'content' => [
                    [
                        'type' => 'hero',
                        'title' => 'Welcome to Our Services',
                        'subtitle' => 'Professional and reliable solutions for all your needs',
                        'background_image' => '/images/hero-bg.jpg',
                        'cta_text' => 'Get Started',
                        'cta_url' => '/contact'
                    ],
                    [
                        'type' => 'features',
                        'title' => 'Why Choose Us',
                        'items' => [
                            [
                                'icon' => 'fas fa-star',
                                'title' => 'Quality Service',
                                'description' => 'We provide top-quality services with attention to detail'
                            ],
                            [
                                'icon' => 'fas fa-clock',
                                'title' => 'Timely Delivery',
                                'description' => 'We respect your time and deliver on schedule'
                            ],
                            [
                                'icon' => 'fas fa-users',
                                'title' => 'Expert Team',
                                'description' => 'Our experienced professionals are here to help'
                            ]
                        ]
                    ],
                    [
                        'type' => 'cta',
                        'title' => 'Ready to Get Started?',
                        'description' => 'Contact us today for a free consultation',
                        'button_text' => 'Contact Us',
                        'button_url' => '/contact'
                    ]
                ]
            ],
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'status' => CmsPageStatus::Published,
                'published_at' => Carbon::now(),
                'description' => 'Learn more about our company, our mission, and our values',
                'keywords' => 'about, company, mission, values, team',
                'content' => [
                    [
                        'type' => 'section',
                        'title' => 'Our Story',
                        'content' => 'Founded with a passion for excellence, we have been serving our community for over a decade. Our commitment to quality and customer satisfaction drives everything we do.'
                    ],
                    [
                        'type' => 'section',
                        'title' => 'Our Mission',
                        'content' => 'To provide exceptional services that exceed our clients\' expectations while maintaining the highest standards of professionalism and integrity.'
                    ],
                    [
                        'type' => 'team',
                        'title' => 'Meet Our Team',
                        'members' => [
                            [
                                'name' => 'John Doe',
                                'position' => 'CEO & Founder',
                                'bio' => 'Visionary leader with 15+ years of industry experience',
                                'image' => '/images/team/john-doe.jpg'
                            ],
                            [
                                'name' => 'Jane Smith',
                                'position' => 'Operations Manager',
                                'bio' => 'Expert in streamlining processes and ensuring quality delivery',
                                'image' => '/images/team/jane-smith.jpg'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Services',
                'slug' => 'services',
                'status' => CmsPageStatus::Published,
                'published_at' => Carbon::now(),
                'description' => 'Discover our comprehensive range of professional services',
                'keywords' => 'services, solutions, professional, expertise',
                'content' => [
                    [
                        'type' => 'services',
                        'title' => 'Our Services',
                        'services' => [
                            [
                                'icon' => 'fas fa-cog',
                                'title' => 'Technical Services',
                                'description' => 'Professional technical solutions for complex problems',
                                'features' => ['Expert technicians', 'Latest technology', 'Fast turnaround']
                            ],
                            [
                                'icon' => 'fas fa-chart-line',
                                'title' => 'Consulting',
                                'description' => 'Strategic consulting to help grow your business',
                                'features' => ['Market analysis', 'Growth strategies', 'Performance optimization']
                            ],
                            [
                                'icon' => 'fas fa-shield-alt',
                                'title' => 'Security Services',
                                'description' => 'Comprehensive security solutions for peace of mind',
                                'features' => ['24/7 monitoring', 'Rapid response', 'Advanced systems']
                            ]
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Contact',
                'slug' => 'contact',
                'status' => CmsPageStatus::Published,
                'published_at' => Carbon::now(),
                'description' => 'Get in touch with us - we\'re here to help',
                'keywords' => 'contact, get in touch, support, help',
                'content' => [
                    [
                        'type' => 'contact',
                        'title' => 'Get In Touch',
                        'description' => 'Ready to start your project? Contact us today for a free consultation.',
                        'email' => 'info@example.com',
                        'phone' => '+1 (555) 123-4567',
                        'address' => '123 Business Ave, Suite 100<br>City, State 12345'
                    ]
                ]
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'status' => CmsPageStatus::Published,
                'published_at' => Carbon::now(),
                'description' => 'Our privacy policy and how we protect your data',
                'keywords' => 'privacy, policy, data protection, GDPR',
                'content' => [
                    [
                        'type' => 'content',
                        'title' => 'Privacy Policy',
                        'content' => 'We are committed to protecting your privacy and ensuring the security of your personal information. This policy explains how we collect, use, and safeguard your data.'
                    ]
                ]
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'status' => CmsPageStatus::Published,
                'published_at' => Carbon::now(),
                'description' => 'Terms and conditions for using our services',
                'keywords' => 'terms, conditions, service, agreement',
                'content' => [
                    [
                        'type' => 'content',
                        'title' => 'Terms of Service',
                        'content' => 'These terms and conditions outline the rules and regulations for the use of our services. By accessing this website and using our services, you accept these terms and conditions in full.'
                    ]
                ]
            ]
        ];

        $created = 0;
        foreach ($pages as $pageData) {
            $page = CmsPage::create([
                'title' => $pageData['title'],
                'slug' => $pageData['slug'],
                'status' => $pageData['status'],
                'published_at' => $pageData['published_at'],
                'description' => $pageData['description'],
                'keywords' => $pageData['keywords'],
                'content' => $pageData['content']
            ]);

            $created++;
            $this->command->info("✅ Created page: {$page->title}");
        }

        $this->command->info("📊 Created {$created} CMS pages");
    }
}