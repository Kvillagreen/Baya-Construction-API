<?php

namespace App\Services\Business;

use App\Models\CRM\PurchaseOrder;
use App\Models\Sales\Opportunity;
use Illuminate\Support\Str;

class PurchaseOrderService
{
    public function __construct(private readonly SalesService $sales, private readonly AuditLogService $audit)
    {
    }

    public function create(array $payload): PurchaseOrder
    {
        $po = PurchaseOrder::query()->create([
            ...$payload,
            'po_number' => $payload['po_number'] ?? 'PO-'.Str::upper(Str::random(8)),
        ]);

        if ($po->sale_id) {
            $sale = Opportunity::query()->find($po->sale_id);
            if ($sale) {
                $this->sales->updateStage($sale, 'Purchase Order Received', 'PO created', $payload['created_by'] ?? null);
            }
        }

        $this->audit->record('created', $po, null, $po->toArray(), $payload['created_by'] ?? null);

        return $po;
    }
}
