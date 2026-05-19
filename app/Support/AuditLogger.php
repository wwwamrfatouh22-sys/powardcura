<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Throwable;

class AuditLogger
{
    public static function log(string $action, ?Model $subject = null, array $metadata = []): void
    {
        try {
            AuditLog::query()->create([
                'user_id' => AuthContext::id(),
                'user_type' => AuthContext::role(),
                'action' => $action,
                'auditable_type' => $subject ? $subject::class : null,
                'auditable_id' => $subject?->getKey(),
                'ip_address' => Request::ip(),
                'metadata' => $metadata,
                'created_at' => now(),
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
