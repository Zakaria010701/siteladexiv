<?php

namespace App\Models;

use App\Enums\Cms\CmsBuilderBlock;
use App\Enums\Cms\CmsPageStatus;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsPage extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => CmsPageStatus::class,
        'content' => 'array',
    ];

    public function scopeSlug(Builder $query, string $slug): void
    {
        $query->where('slug', $slug);
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('status', CmsPageStatus::Published);
    }

}
