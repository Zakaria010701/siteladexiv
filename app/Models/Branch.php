<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Branch
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string $name
 * @property string $short_code
 * @property string $calendar_start_time
 * @property string $calendar_end_time
 * @property string $frontend_start_time
 * @property string $frontend_end_time
 * @property array $open_days
 * @property-read Collection<int, Room> $rooms
 * @property-read int|null $rooms_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static Builder|Branch newModelQuery()
 * @method static Builder|Branch newQuery()
 * @method static Builder|Branch onlyTrashed()
 * @method static Builder|Branch query()
 * @method static Builder|Branch whereCalendarEndTime($value)
 * @method static Builder|Branch whereCalendarStartTime($value)
 * @method static Builder|Branch whereCreatedAt($value)
 * @method static Builder|Branch whereDeletedAt($value)
 * @method static Builder|Branch whereFrontendEndTime($value)
 * @method static Builder|Branch whereFrontendStartTime($value)
 * @method static Builder|Branch whereId($value)
 * @method static Builder|Branch whereName($value)
 * @method static Builder|Branch whereOpenDays($value)
 * @method static Builder|Branch whereShortCode($value)
 * @method static Builder|Branch whereUpdatedAt($value)
 * @method static Builder|Branch withTrashed()
 * @method static Builder|Branch withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'open_days' => 'array',
    ];

    public function availabilities(): MorphMany
    {
        return $this->morphMany(Availability::class, 'planable');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function notificationTemplates(): BelongsToMany
    {
        return $this->belongsToMany(NotificationTemplate::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }
}
