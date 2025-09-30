<?php

namespace App\Enums\Transactions;

enum FintsImportStatus: string
{
    case Pending = 'pending';
    case Needs2FA = 'needs2fa';
    case Done = 'done';

    public function isPending(): bool
    {
        return $this === self::Pending;
    }

    public function needs2FA(): bool
    {
        return $this === self::Needs2FA;
    }

    public function isDone(): bool
    {
        return $this === self::Done;
    }
}
