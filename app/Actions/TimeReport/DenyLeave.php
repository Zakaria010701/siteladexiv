<?php

namespace App\Actions\TimeReport;

use App\Models\Leave;
use App\Models\User;

class DenyLeave
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
        $this->leave->approved_at = null;
        $this->leave->denied_at = now();
        $this->leave->processed_by_id = $this->processor->id;
        $this->leave->save();

        return $this->leave;
    }
}
