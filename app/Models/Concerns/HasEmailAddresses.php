<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Collection;
use App\Models\EmailAddress;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property-read Collection<int, EmailAddress> $emailAddresses
 * @property-read int|null $email_addresses_count
 */
trait HasEmailAddresses
{
    public function emailAddresses(): MorphMany
    {
        return $this->morphMany(EmailAddress::class, 'addressable');
    }
}
