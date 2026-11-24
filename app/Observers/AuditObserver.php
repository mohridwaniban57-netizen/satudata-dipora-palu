<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    public function created(Model $model): void
    {
        $this->logAction($model, 'create', null, $this->sanitizeValues($model->getAttributes()));
    }

    public function updated(Model $model): void
    {
        $oldValues = $this->sanitizeValues($model->getOriginal());
        $newValues = $this->sanitizeValues($model->getChanges());

        // Keep only attributes that actually changed in oldValues
        $filteredOld = array_intersect_key($oldValues, $newValues);

        $this->logAction($model, 'update', $filteredOld, $newValues);
    }

    public function deleted(Model $model): void
    {
        $this->logAction($model, 'delete', $this->sanitizeValues($model->getAttributes()), null);
    }

    public function restored(Model $model): void
    {
        $this->logAction($model, 'restore', null, $this->sanitizeValues($model->getAttributes()));
    }

    protected function logAction(Model $model, string $action, ?array $oldValues, ?array $newValues): void
    {
        try {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'entity' => class_basename($model),
                'entity_id' => (string) $model->getKey(),
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Fail silently or log to avoid breaking business transaction if audit logging encounters an issue
            report($e);
        }
    }

    protected function sanitizeValues(array $attributes): array
    {
        unset($attributes['password'], $attributes['remember_token']);
        return $attributes;
    }
}
