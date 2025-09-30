<?php

namespace App\Models\Concerns\Appointments;

use Illuminate\Support\Carbon;
use App\Enums\Appointments\AppointmentStatus;
use App\Enums\Appointments\CancelReason;
use App\Events\Appointments\AppointmentApprovedEvent;
use App\Events\Appointments\AppointmentCanceledEvent;
use App\Events\Appointments\AppointmentDoneEvent;
use App\Events\Appointments\AppointmentPendingEvent;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property Carbon $canceled_at
 * @property Carbon $done_at
 * @property Carbon $approved_at
 * @property AppointmentStatus $status
 */
trait HasStatus
{
    public function isPending(): bool
    {
        return $this->status->isPending();
    }

    public function isApproved(): bool
    {
        return $this->status->isApproved();
    }

    public function isDone(): bool
    {
        return $this->status->isDone();
    }

    public function isCanceled(): bool
    {
        return $this->status->isCanceled();
    }

    public function setStatus(AppointmentStatus $status): static
    {
        return match ($status) {
            AppointmentStatus::Approved => $this->markApproved(),
            AppointmentStatus::Done => $this->markDone(),
            AppointmentStatus::Pending => $this->markPending(),
            AppointmentStatus::Canceled => $this->markCanceled(),
            //default => $this,
        };
    }

    public function markPending(bool $sendNotification = true): static
    {
        $old = clone $this;
        $this->canceled_at = null;
        $this->done_at = null;
        $this->approved_at = null;
        $this->status = AppointmentStatus::Pending;
        $this->save();

        AppointmentPendingEvent::dispatch($this, auth()->user(), $sendNotification);

        return $this;
    }

    public function markApproved(bool $sendNotification = true): static
    {
        $old = clone $this;
        $this->canceled_at = null;
        $this->done_at = null;
        $this->approved_at = $this->approved_at ?? now();
        $this->status = AppointmentStatus::Approved;
        $this->save();

        AppointmentApprovedEvent::dispatch($this, auth()->user(), $sendNotification);

        return $this;
    }

    public function markCanceled(bool $sendNotification = true, ?CancelReason $cancelReason = null): static
    {
        $old = clone $this;
        $this->canceled_at = $this->canceled_at ?? now();
        $this->done_at = null;
        $this->approved_at = null;
        $this->status = AppointmentStatus::Canceled;
        if (isset($cancelReason)) {
            $this->cancel_reason = $cancelReason;
        }
        $this->save();

        AppointmentCanceledEvent::dispatch($this, auth()->user(), $this->cancel_reason ?? CancelReason::Other,  $sendNotification);

        return $this;
    }

    public function markDone(bool $sendNotification = true): static
    {
        $old = clone $this;
        $this->canceled_at = null;
        $this->done_at = $this->done_at ?? now();
        $this->approved_at = null;
        $this->status = AppointmentStatus::Done;
        $this->save();

        AppointmentDoneEvent::dispatch($this, auth()->user(), $sendNotification);

        return $this;
    }

    public function scopeStatus(Builder $query, AppointmentStatus $status, string $operator = '='): void
    {
        $query->where('status', $operator, $status->value);
    }

    public function scopeNotCanceled(Builder $builder): void
    {
        $builder->where('status', '!=', AppointmentStatus::Canceled);
    }
}
