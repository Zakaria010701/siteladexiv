<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeaderContact extends Model
{
    protected $table = 'header_contact';

    protected $fillable = [
        'welcome_text',
        'phone',
        'email',
        'address',
        'facebook_url',
        'instagram_url',
        'tiktok_url',
        'german_flag_icon',
        'english_flag_icon',
        'is_active',
        'position',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position' => 'integer',
    ];

    /**
     * Get the CMS menu item that references this header contact
     */
    public function cmsMenuItem()
    {
        return $this->morphOne(CmsMenuItem::class, 'reference');
    }

    /**
     * Get default header contact (active one with lowest position)
     */
    public static function getDefault()
    {
        return static::where('is_active', true)
            ->orderBy('position')
            ->first();
    }
}
