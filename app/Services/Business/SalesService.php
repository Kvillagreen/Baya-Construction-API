<?php

namespace App\Services\Business;

use App\Models\CRM\Customer;
use App\Models\CRM\Project;
use App\Models\Sales\Opportunity;
use App\Models\Sales\SalesLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesService
{
    public function __construct(private readonly CustomerService $customers, private readonly AuditLogService $audit)
    {
    }

    public function create(array $payload): Opportunity
    {
        return DB::transaction(function () use ($payload): Opportunity {
            $customer = null;

            if (!empty($payload['customer_id'])) {
                $customer = Customer::query()->findOrFail($payload['customer_id']);
            } elseif (!empty($payload['customer'])) {
                $customer = $this->customers->create($payload['customer']);
            }

            $sale = Opportunity::query()->create([
                'customer_id' => $customer?->id,
                'project_id' => null,
                'quotation_id' => null,
                'sales_code' => $payload['sales_code'] ?? 'SALE-'.Str::upper(Str::random(8)),
                'title' => $payload['title'] ?? $payload['project'] ?? 'Untitled Sale',
                'description' => $payload['description'] ?? null,
                'account_name' => $payload['account_name'] ?? $customer?->company_name ?? 'Unassigned Account',
                'project' => $payload['project'] ?? 'General',
                'stage' => $payload['stage'] ?? 'Lead',
                'status' => $payload['status'] ?? 'Open',
                'amount' => $payload['amount'] ?? 0,
                'owner_name' => $payload['owner_name'] ?? 'Unassigned',
                'probability' => $payload['probability'] ?? 0,
                'close_date' => $payload['close_date'] ?? null,
                'expected_close_date' => $payload['expected_close_date'] ?? null,
                'assigned_to' => $payload['assigned_to'] ?? null,
                'created_by' => $payload['created_by'] ?? null,
            ]);

            if (!empty($payload['project_details'])) {
                $project = Project::query()->create([
                    'customer_id' => $customer?->id,
                    'sale_id' => $sale->id,
                    'project_name' => $payload['project_details']['title'] ?? $sale->project,
                    'title' => $payload['project_details']['title'] ?? $sale->project,
                    'description' => $payload['project_details']['description'] ?? null,
                    'scope_of_work' => $payload['project_details']['scope_of_work'] ?? null,
                    'estimated_price' => $payload['project_details']['estimated_price'] ?? $sale->amount,
                    'approved_price' => $payload['project_details']['approved_price'] ?? 0,
                    'status' => $payload['project_details']['status'] ?? 'pending',
                ]);

                $sale->project_id = $project->id;
                $sale->save();
            }

            SalesLog::query()->create([
                'sale_id' => $sale->id,
                'stage_from' => null,
                'stage_to' => $sale->stage,
                'remarks' => 'Sale created',
                'created_by' => $payload['created_by'] ?? null,
            ]);

            $this->audit->record('created', $sale, null, $sale->toArray(), $payload['created_by'] ?? null);

            return $sale;
        });
    }

    public function updateStage(Opportunity $sale, string $stage, ?string $remarks = null, ?string $performedBy = null): Opportunity
    {
        $before = $sale->toArray();
        $from = $sale->stage;
        $sale->stage = $stage;
        $sale->save();

        SalesLog::query()->create([
            'sale_id' => $sale->id,
            'stage_from' => $from,
            'stage_to' => $stage,
            'remarks' => $remarks,
            'created_by' => $performedBy,
        ]);

        $this->audit->record('stage_updated', $sale, $before, $sale->fresh()->toArray(), $performedBy);

        return $sale;
    }
}
