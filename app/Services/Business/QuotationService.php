<?php

namespace App\Services\Business;

use App\Models\CRM\Quotation;
use App\Models\CRM\QuotationItem;
use App\Models\Sales\Opportunity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuotationService
{
    public function __construct(private readonly SalesService $sales, private readonly AuditLogService $audit)
    {
    }

    public function create(array $payload): Quotation
    {
        return DB::transaction(function () use ($payload): Quotation {
            $items = collect($payload['items'] ?? []);
            $subtotal = round($items->sum(fn (array $item) => ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0)), 2);
            $discount = (float) ($payload['discount'] ?? 0);
            $tax = (float) ($payload['tax'] ?? 0);
            $total = round($subtotal - $discount + $tax, 2);

            $quotation = Quotation::query()->create([
                'quotation_code' => $payload['quotation_code'] ?? 'QUO-'.Str::upper(Str::random(8)),
                'customer_id' => $payload['customer_id'] ?? null,
                'project_id' => $payload['project_id'],
                'sale_id' => $payload['sale_id'] ?? null,
                'title' => $payload['title'] ?? 'Quotation',
                'description' => $payload['description'] ?? null,
                'scope_of_work' => $payload['scope_of_work'] ?? null,
                'amount' => $total,
                'content' => $payload['description'] ?? '',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total_amount' => $total,
                'status' => $payload['status'] ?? 'Draft',
                'valid_until' => $payload['valid_until'] ?? null,
                'prepared_by' => $payload['prepared_by'] ?? null,
                'approved_by' => $payload['approved_by'] ?? null,
                'quotation_price_encrypted' => $payload['quotation_price_encrypted'] ?? $total,
            ]);

            $quotation->items()->createMany($items->map(fn (array $item) => [
                'item_name' => $item['item_name'],
                'description' => $item['description'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => round($item['quantity'] * $item['unit_price'], 2),
            ])->all());

            if (!empty($payload['sale_id'])) {
                Opportunity::query()->whereKey($payload['sale_id'])->update(['quotation_id' => $quotation->id, 'stage' => 'Quotation Sent']);
            }

            $this->audit->record('created', $quotation, null, $quotation->toArray(), $payload['prepared_by'] ?? null);

            return $quotation->load('items');
        });
    }

    public function accept(Quotation $quotation, ?string $performedBy = null): Quotation
    {
        $before = $quotation->toArray();
        $quotation->update(['status' => 'Converted', 'approved_by' => $performedBy]);

        if ($quotation->sale_id) {
            $sale = Opportunity::query()->find($quotation->sale_id);
            if ($sale) {
                $this->sales->updateStage($sale, 'Purchase Order Received', 'Quotation accepted', $performedBy);
            }
        }

        $this->audit->record('accepted', $quotation, $before, $quotation->fresh()->toArray(), $performedBy);

        return $quotation->fresh();
    }
}
