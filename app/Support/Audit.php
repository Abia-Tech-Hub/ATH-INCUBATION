<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Support\Str;

/** Append-only record of sensitive actions (sign-ins, team changes, 2FA). Only owners can read it. */
class Audit
{
    public static function log(?int $actorId, string $action, ?string $target = null): void
    {
        try {
            AuditLog::create([
                'actor_id' => $actorId,
                'action' => $action,
                'target' => $target ? Str::limit($target, 200, '') : null,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
