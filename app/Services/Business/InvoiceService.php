<?php

namespace App\Services\Business;

use App\Models\CRM\Invoice;
use App\Models\Sales\Opportunity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceService
{
    public function __construct(private readonly SalesService $sales, private readonly AuditLogService $audit)
    {
    }

    public function create(array $payload): Invoice
    {
        return DB::transaction(function () use ($payload): Invoice {
            $items = collect($payload['items'] ?? []);
            $subtotal = round($items->sum(fn (array $item) => ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0)), 2);
            $discount = (float) ($payload['discount'] ?? 0);
            $tax = (float) ($payload['tax'] ?? 0);
            $total = round($subtotal - $discount + $tax, 2);

            $invoice = Invoice::query()->create([
                'invoice_number' => $payload['invoice_number'] ?? 'INV-'.Str::upper(Str::random(8)),
                'customer_id' => $payload['customer_id'],
                'project_id' => $payload['project_id'] ?? null,
                'sale_id' => $payload['sale_id'] ?? null,
                'quotation_id' => $payload['quotation_id'] ?? null,
                'purchase_order_id' => $payload['purchase_order_id'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total_amount' => $total,
                'paid_amount' => 0,
                'balance_due' => $total,
                'status' => $payload['status'] ?? 'Draft',
                'due_date' => $payload['due_date'] ?? null,
                'issued_date' => $payload['issued_date'] ?? now()->toDateString(),
            ]);

            $invoice->items()->createMany($items->map(fn (array $item) => [
                'item_name' => $item['item_name'],
                'description' => $item['description'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => round($item['quantity'] * $item['unit_price'], 2),
            ])->all());

            if ($invoice->sale_id) {
                $sale = Opportunity::query()->find($invoice->sale_id);
                if ($sale) {
                    $this->sales->updateStage($sale, 'Invoiced', 'Invoice created');
                }
            }

            $this->audit->record('created', $invoice, null, $invoice->toArray());

            return $invoice->load('items');
        });
    }
}
