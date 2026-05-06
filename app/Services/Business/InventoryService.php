<?php

namespace App\Services\Business;

use App\Models\Inventory\InventoryItem;
use App\Models\Inventory\InventoryTransaction;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function __construct(private readonly AuditLogService $audit)
    {
    }

    public function transact(InventoryItem $item, array $payload): InventoryTransaction
    {
        return DB::transaction(function () use ($item, $payload): InventoryTransaction {
            $previous = (float) ($item->quantity_on_hand ?: $item->available_stock);
            $quantity = (float) $payload['quantity'];
            $type = $payload['transaction_type'];
            $delta = in_array($type, ['Stock Out', 'Reserved', 'Damaged', 'Lost'], true) ? -$quantity : $quantity;
            $new = round($previous + $delta, 2);

            if ($new < 0 && $type !== 'Adjustment') {
                throw new \InvalidArgumentException('Negative inventory quantity is not allowed for this transaction.');
            }

            $item->quantity_on_hand = $new;
            $item->available_stock = (int) max(0, floor($new));
            $item->save();

            $transaction = InventoryTransaction::query()->create([
                ...$payload,
                'item_id' => $item->id,
                'previous_quantity' => $previous,
                'new_quantity' => $new,
                'transaction_date' => $payload['transaction_date'] ?? now(),
            ]);

            $this->audit->record('inventory_transacted', $item, ['quantity_on_hand' => $previous], ['quantity_on_hand' => $new], $payload['performed_by'] ?? null);

            return $transaction;
        });
    }
}
