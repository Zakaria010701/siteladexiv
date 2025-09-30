<?php

namespace App\Models\Concerns;

use App\Enums\Verifications\VerificationStatus;
use App\Models\Verification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait CanBeVerified
{
    public function verifications(): MorphMany
    {
        return $this->morphMany(Verification::class, 'verifiable');
    }

    public function currentVerification(): MorphOne
    {
        return $this->morphOne(Verification::class, 'verifiable')->latestOfMany();
    }

    public function isVerified(): bool
    {
        return $this->verificationStatus() == VerificationStatus::Pass;
    }
    public function isNotVerified(): bool
    {
        return $this->verificationStatus() != VerificationStatus::Pass;
    }
    public function verificationStatus(): VerificationStatus
    {
        return $this->currentVerification?->status ?? VerificationStatus::Unverified;
    }

    public function scopeVerified(Builder $query): void
    {
        $query->whereHas('currentVerification', fn(Verification|Builder $query) => $query->status(VerificationStatus::Pass));
    }
    public function scopeNotVerified(Builder $query): void
    {
        $query->whereDoesntHave('currentVerification', fn(Verification|Builder $query) => $query->status(VerificationStatus::Pass));
    }
}
