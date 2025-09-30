<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\Notifications\NotificationChannel;
use App\Enums\Notifications\NotificationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\NotificationTemplate
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $branch_id
 * @property NotificationType $type
 * @property NotificationChannel $channel
 * @property bool $is_enabled
 * @property string $subject
 * @property array $content
 * @property-read Branch|null $branch
 *
 * @method static Builder|NotificationTemplate newModelQuery()
 * @method static Builder|NotificationTemplate newQuery()
 * @method static Builder|NotificationTemplate query()
 * @method static Builder|NotificationTemplate whereBranchId($value)
 * @method static Builder|NotificationTemplate whereChannel($value)
 * @method static Builder|NotificationTemplate whereContent($value)
 * @method static Builder|NotificationTemplate whereCreatedAt($value)
 * @method static Builder|NotificationTemplate whereId($value)
 * @method static Builder|NotificationTemplate whereIsEnabled($value)
 * @method static Builder|NotificationTemplate whereSubject($value)
 * @method static Builder|NotificationTemplate whereType($value)
 * @method static Builder|NotificationTemplate whereUpdatedAt($value)
 *
 * @property bool $enable_mail
 * @property bool $enable_sms
 * @property string|null $sms_content
 *
 * @method static Builder|NotificationTemplate whereEnableMail($value)
 * @method static Builder|NotificationTemplate whereEnableSms($value)
 * @method static Builder|NotificationTemplate whereSmsContent($value)
 *
 * @mixin \Eloquent
 */
class NotificationTemplate extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => NotificationType::class,
        'content' => 'array',
        'is_enabled' => 'boolean',
        'enable_mail' => 'boolean',
        'enable_sms' => 'boolean',
    ];

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class);
    }

    public function appointmentReminderSettings(): HasMany
    {
        return $this->hasMany(AppointmentReminderSetting::class);
    }
}
