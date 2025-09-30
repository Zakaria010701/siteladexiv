<?php

namespace App\Actions\Appointments;

use App\Enums\Appointments\AppointmentType;
use App\Models\Service;
use App\Models\ServicePackageDuration;
use Illuminate\Support\Collection;

class CalculateDuration
{
    public function __construct(
        private AppointmentType $appointmentType,
        private Collection $services,
    ) {}

    public static function make(
        string|AppointmentType $appointmentType,
        array|Collection $services
    ): self {
        if (is_string($appointmentType)) {
            $appointmentType = AppointmentType::from($appointmentType);
        }

        if (is_array($services)) {
            $services = Service::whereIn('id', $services)->get();
        }

        return new self(
            $appointmentType,
            $services,
        );
    }

    public function execute(): int
    {
        $duration = 0;

        // If the appointment type is consultation just return it's default duration.
        if ($this->appointmentType->isConsultation()) {
            return $this->appointmentType->getDefaultDuration();
        }

        $minDuration = $this->services->max('duration');

        $duration = $this->services->sum('duration');

        // Reduce the total duration by the package percentage.
        $percentage = $this->findPackageDurationPercentage();
        $duration = $duration * ((100 - $percentage) / 100);

        //$remaining = $duration%general()->default_time_slot;
        //if($remaining>0) $duration = $duration-$remaining+general()->default_time_slot;

        // If the duration is smaller than the min duration, just return the min duration.
        if ($minDuration > $duration) {
            return $minDuration;
        }

        // If the duration is smaller than the min duration, just return the min duration.
        if (frontend()->min_duration > $duration) {
            return frontend()->min_duration;
        }

        // If the duration is greater than the max duration, just return the max duration.
        if (frontend()->max_duration < $duration) {
            return frontend()->max_duration;
        }

        return $duration;
    }

    private function findPackageDurationPercentage(): int
    {
        $count = $this->services->count();

        $packageDuration = ServicePackageDuration::query()
            ->where('count', '<=', $count)
            ->orderByDesc('count')
            ->first();

        if (is_null($packageDuration)) {
            return 0;
        }

        return $packageDuration->percentage;
    }
}
