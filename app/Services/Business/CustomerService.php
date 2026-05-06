<?php

namespace App\Services\Business;

use App\Models\CRM\Customer;
use App\Models\CRM\CustomerActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerService
{
    public function __construct(private readonly AuditLogService $audit)
    {
    }

    public function create(array $payload): Customer
    {
        return DB::transaction(function () use ($payload): Customer {
            $customer = Customer::query()->create([
                ...$payload,
                'customer_code' => $payload['customer_code'] ?? 'CUST-'.Str::upper(Str::random(8)),
            ]);

            CustomerActivity::query()->create([
                'customer_id' => $customer->id,
                'activity_type' => 'customer_created',
                'subject' => 'Customer record created',
                'notes' => $payload['notes'] ?? null,
                'activity_date' => now(),
                'created_by' => $payload['created_by'] ?? null,
            ]);

            $this->audit->record('created', $customer, null, $customer->toArray(), $payload['created_by'] ?? null);

            return $customer;
        });
    }
}
