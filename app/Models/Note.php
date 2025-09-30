<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Note
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $notable_type
 * @property int|null $notable_id
 * @property int|null $user_id
 * @property int|null $customer_id
 * @property string $content
 * @property bool $is_important
 * @property-read Model|\Eloquent $notable
 *
 * @method static Builder|Note newModelQuery()
 * @method static Builder|Note newQuery()
 * @method static Builder|Note query()
 * @method static Builder|Note whereContent($value)
 * @method static Builder|Note whereCreatedAt($value)
 * @method static Builder|Note whereCustomerId($value)
 * @method static Builder|Note whereId($value)
 * @method static Builder|Note whereIsImportant($value)
 * @method static Builder|Note whereNotableId($value)
 * @method static Builder|Note whereNotableType($value)
 * @method static Builder|Note whereUpdatedAt($value)
 * @method static Builder|Note whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Note extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_important' => 'boolean',
    ];

    public function notable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
