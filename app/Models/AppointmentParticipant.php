<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

/**
 * App\Models\AppointmentParticipant
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $appointment_id
 * @property string $name
 * @property string $email
 * @property string|null $phone_number
 *
 * @method static Builder|AppointmentParticipant newModelQuery()
 * @method static Builder|AppointmentParticipant newQuery()
 * @method static Builder|AppointmentParticipant query()
 * @method static Builder|AppointmentParticipant whereAppointmentId($value)
 * @method static Builder|AppointmentParticipant whereCreatedAt($value)
 * @method static Builder|AppointmentParticipant whereEmail($value)
 * @method static Builder|AppointmentParticipant whereId($value)
 * @method static Builder|AppointmentParticipant whereName($value)
 * @method static Builder|AppointmentParticipant wherePhoneNumber($value)
 * @method static Builder|AppointmentParticipant whereUpdatedAt($value)
 *
 * @property-read Appointment|null $appointment
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 *
 * @mixin \Eloquent
 */
class AppointmentParticipant extends Model
{
    use HasFactory;
    use Notifiable;

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return array<string, string>|string
     */
    public function routeNotificationForMail(Notification $notification): array|string
    {
        return [$this->email => $this->name];
    }
}
