<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string $name
 * @property string $short_code
 * @property string $color
 * @property string $text_color
 *
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category onlyTrashed()
 * @method static Builder|Category query()
 * @method static Builder|Category whereColor($value)
 * @method static Builder|Category whereCreatedAt($value)
 * @method static Builder|Category whereDeletedAt($value)
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereName($value)
 * @method static Builder|Category whereShortCode($value)
 * @method static Builder|Category whereTextColor($value)
 * @method static Builder|Category whereUpdatedAt($value)
 * @method static Builder|Category withTrashed()
 * @method static Builder|Category withoutTrashed()
 *
 * @property-read Collection<int, AppointmentExtra> $appointmentExtras
 * @property-read int|null $appointment_extras_count
 * @property-read Collection<int, Appointment> $appointments
 * @property-read int|null $appointments_count
 *
 * @mixin \Eloquent
 */
class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function appointmentExtras(): BelongsToMany
    {
        return $this->belongsToMany(AppointmentExtra::class)->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function consultingUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'consultation_category_user', 'category_id', 'user_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(ServicePackage::class);
    }
}
