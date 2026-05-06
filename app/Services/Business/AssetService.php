<?php

namespace App\Services\Business;

use App\Models\Assets\Asset;
use App\Models\Assets\AssetAssignment;
use App\Models\Assets\AssetLog;
use Illuminate\Support\Facades\DB;

class AssetService
{
    public function __construct(private readonly AuditLogService $audit)
    {
    }

    public function assign(Asset $asset, array $payload, string $newStatus = 'Assigned'): AssetAssignment
    {
        return DB::transaction(function () use ($asset, $payload, $newStatus): AssetAssignment {
            if (!in_array($asset->status, ['Available', 'Assigned'], true) && $newStatus !== 'Released') {
                throw new \InvalidArgumentException('Asset is not available for this action.');
            }

            $previousStatus = $asset->status;
            $assignment = AssetAssignment::query()->create([
                'asset_id' => $asset->id,
                'assigned_to' => $payload['assigned_to'] ?? null,
                'released_by' => $payload['released_by'] ?? null,
                'assigned_date' => $payload['assigned_date'] ?? now()->toDateString(),
                'expected_return_date' => $payload['expected_return_date'] ?? null,
                'returned_date' => $payload['returned_date'] ?? null,
                'status' => $payload['status'] ?? $newStatus,
                'notes' => $payload['notes'] ?? null,
            ]);

            $asset->status = $newStatus;
            $asset->save();

            AssetLog::query()->create([
                'asset_id' => $asset->id,
                'action' => strtolower($newStatus),
                'previous_status' => $previousStatus,
                'new_status' => $newStatus,
                'assigned_to' => $payload['assigned_to'] ?? null,
                'released_to' => $payload['released_to'] ?? null,
                'borrowed_by' => $payload['borrowed_by'] ?? null,
                'remarks' => $payload['notes'] ?? null,
                'performed_by' => $payload['performed_by'] ?? null,
            ]);

            $this->audit->record('asset_'.$newStatus, $asset, ['status' => $previousStatus], ['status' => $newStatus], $payload['performed_by'] ?? null);

            return $assignment;
        });
    }
}
