<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class DeletionGuard
{
    /**
     * Delete exactly one already-loaded model and audit the operation.
     */
    public static function deleteOne(Model $model, string $action, array $metadata = []): void
    {
        if (! $model->exists || $model->getKey() === null) {
            throw new InvalidArgumentException('Delete blocked: a concrete persisted record ID is required.');
        }

        $payload = $metadata + [
            'model' => $model::class,
            'record_id' => $model->getKey(),
        ];

        AuditLogger::log($action . '.requested', $model, $payload);

        $model->delete();

        AuditLogger::log($action, $model, $payload);
    }
}
