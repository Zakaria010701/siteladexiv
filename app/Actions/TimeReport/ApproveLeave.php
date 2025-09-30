<?php

namespace App\Actions\TimeReport;

use App\Models\Availability;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ApproveLeave
{
    public function __construct(
        private Leave $leave,
        private readonly User $processor
    ) {}

    public static function make(Leave $leave, User $processor): self
    {
        return new self($leave, $processor);
    }

    public function execute(): Leave
    {
        $this->leave->approved_at = now();
        $this->leave->denied_at = null;
        $this->leave->processed_by_id = $this->processor->id;
        $this->leave->save();

        /** @var null|Availability */
        $availability = $this->leave->user->availabilities()
            ->where('start_date', '<=', $this->leave->from)
            ->where(fn (Builder $query) => $query
                ->where('end_date', '>=', $this->leave->from)
                ->orWhereNull('end_date'))
            ->first();

        if(isset($availability)) {
            $this->leave->availabilityAbsence()->create([
                'availability_id' => $availability->id,
                'start_date' => $this->leave->from,
                'end_date' => $this->leave->till,
            ]);
        }

        return $this->leave;
    }
}
