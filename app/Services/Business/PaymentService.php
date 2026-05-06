<?php

namespace App\Services\Business;

use App\Models\CRM\Invoice;
use App\Models\CRM\Payment;
use App\Models\Sales\Opportunity;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(private readonly SalesService $sales, private readonly AuditLogService $audit)
    {
    }

    public function create(array $payload): Payment
    {
        return DB::transaction(function () use ($payload): Payment {
            $invoice = Invoice::query()->findOrFail($payload['invoice_id']);
            $amount = (float) $payload['amount'];

            if ($amount > (float) $invoice->balance_due) {
                throw new \InvalidArgumentException('Payment amount exceeds invoice balance.');
            }

            $payment = Payment::query()->create($payload);

            $invoice->paid_amount = round((float) $invoice->paid_amount + $amount, 2);
            $invoice->balance_due = round((float) $invoice->total_amount - (float) $invoice->paid_amount, 2);
            $invoice->status = $invoice->balance_due <= 0 ? 'Paid' : 'Partially Paid';
            $invoice->paid_at = $invoice->balance_due <= 0 ? now() : null;
            $invoice->save();

            if ($invoice->sale_id) {
                $sale = Opportunity::query()->find($invoice->sale_id);
                if ($sale) {
                    $this->sales->updateStage($sale, $invoice->balance_due <= 0 ? 'Paid' : 'Invoiced', 'Payment received', $payload['received_by'] ?? null);
                }
            }

            $this->audit->record('created', $payment, null, $payment->toArray(), $payload['received_by'] ?? null);

            return $payment;
        });
    }
}
