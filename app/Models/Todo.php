<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\Todos\TodoPriority;
use App\Enums\Todos\TodoStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Todo
 *
 * @property int $id
 * @property int $priority
 * @property string|null $due_date
 * @property string|null $category
 * @property int $assigned_to
 * @property int|null $client
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $status
 * @property-read User|null $assignedTo
 * @property-read Customer|null $customerId
 *
 * @method static Builder|Todo newModelQuery()
 * @method static Builder|Todo newQuery()
 * @method static Builder|Todo query()
 * @method static Builder|Todo whereAssignedTo($value)
 * @method static Builder|Todo whereCategory($value)
 * @method static Builder|Todo whereClient($value)
 * @method static Builder|Todo whereCreatedAt($value)
 * @method static Builder|Todo whereDescription($value)
 * @method static Builder|Todo whereDueDate($value)
 * @method static Builder|Todo whereId($value)
 * @method static Builder|Todo wherePriority($value)
 * @method static Builder|Todo whereStatus($value)
 * @method static Builder|Todo whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Todo extends Model
{
    protected $guarded = ['id'];

    public $casts = [
        'status' => TodoStatus::class,
        'priority' => TodoPriority::class,
    ];

    public function progress(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->todoItems->count() > 0) ? (round(($this->todoItems->reject(fn(TodoItem $item) => is_null($item->completed_at))->count() / $this->todoItems->count()) * 100)) : 0
        );
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function todoItems(): HasMany
    {
        return $this->hasMany(TodoItem::class);
    }
}
