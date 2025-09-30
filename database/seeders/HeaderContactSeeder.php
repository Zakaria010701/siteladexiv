<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HeaderContact;

class HeaderContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HeaderContact::create([
            'welcome_text' => 'Welcome to our website',
            'phone' => '069 2108 9619',
            'email' => 'Kontakt-Formular',
            'address' => 'Konrad 15, Frankfurt Friedrichstraße 57, Wiesbaden',
            'facebook_url' => null, // Add your Facebook URL here if available
            'instagram_url' => null, // Add your Instagram URL here if available
            'tiktok_url' => null, // Add your TikTok URL here if available
            'german_flag_icon' => '<svg class="w-6 h-4" fill="currentColor" viewBox="0 0 32 24"><rect width="32" height="8" fill="#000"/><rect y="8" width="32" height="8" fill="#DD0000"/><rect y="16" width="32" height="8" fill="#FFCE00"/></svg>',
            'english_flag_icon' => '<svg class="w-6 h-4" fill="currentColor" viewBox="0 0 60 30"><rect width="60" height="30" fill="#012169"/><path d="M0,0 L60,30 M60,0 L0,30" stroke="#FFF" stroke-width="6"/><path d="M0,0 L60,30 M60,0 L0,30" stroke="#C8102E" stroke-width="4"/><path d="M30,0 V30 M0,15 H60" stroke="#FFF" stroke-width="10"/><path d="M30,0 V30 M0,15 H60" stroke="#C8102E" stroke-width="6"/></svg>',
            'is_active' => true,
            'position' => 0,
        ]);
    }
}
