<?php

namespace App\Services\Business;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogService
{
    public function record(string $action, Model $entity, ?array $before = null, ?array $after = null, ?string $performedBy = null, ?string $ipAddress = null): void
    {
        AuditLog::query()->create([
            'entity_type' => $entity::class,
            'entity_id' => (string) $entity->getKey(),
            'action' => $action,
            'before_data' => $before,
            'after_data' => $after,
            'performed_by' => $performedBy,
            'ip_address' => $ipAddress,
        ]);
    }
}
