<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\StorePayrollAdjustmentRequest;
use App\Models\HR\PayrollAdjustment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PayrollAdjustmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $hasPaidAmount = $this->hasColumn('paid_amount');
        $hasFrequency = $this->hasColumn('frequency');
        $hasPayrollCount = $this->hasColumn('payroll_count');

        return response()->json([
            'data' => PayrollAdjustment::query()
                ->when($request->filled('employee_id'), fn ($query) => $query->where('employee_id', $request->string('employee_id')))
                ->latest()
                ->get(),
        ])->tap(function (JsonResponse $response) use ($hasPaidAmount, $hasFrequency, $hasPayrollCount): void {
            $payload = $response->getData(true);

            $payload['data'] = collect($payload['data'] ?? [])
                ->map(function (array $adjustment) use ($hasPaidAmount, $hasFrequency, $hasPayrollCount): array {
                    if (! $hasPaidAmount) {
                        $adjustment['paid_amount'] = 0;
                    }

                    if (! $hasFrequency) {
                        $adjustment['frequency'] = 'One Time';
                    }

                    if (! $hasPayrollCount) {
                        $adjustment['payroll_count'] = 1;
                    }

                    return $adjustment;
                })
                ->values()
                ->all();

            $response->setData($payload);
        });
    }

    public function store(StorePayrollAdjustmentRequest $request): JsonResponse
    {
        $adjustment = PayrollAdjustment::query()->create($this->filterPersistablePayload($request->validated()));

        return response()->json([
            'message' => 'Payroll adjustment saved successfully.',
            'data' => $this->normalizeResponseData($adjustment->toArray()),
        ], 201);
    }

    public function update(StorePayrollAdjustmentRequest $request, PayrollAdjustment $payrollAdjustment): JsonResponse
    {
        $payrollAdjustment->fill($this->filterPersistablePayload($request->validated()))->save();

        return response()->json([
            'message' => 'Payroll adjustment updated successfully.',
            'data' => $this->normalizeResponseData($payrollAdjustment->toArray()),
        ]);
    }

    public function destroy(PayrollAdjustment $payrollAdjustment): JsonResponse
    {
        $payrollAdjustment->delete();

        return response()->json([
            'message' => 'Payroll adjustment deleted successfully.',
        ]);
    }

    private function filterPersistablePayload(array $payload): array
    {
        $columns = collect([
            'employee_id',
            'label',
            'amount',
            'paid_amount',
            'type',
            'frequency',
            'payroll_count',
        ])->filter(fn (string $column) => $this->hasColumn($column));

        return collect($payload)
            ->only($columns->all())
            ->all();
    }

    private function normalizeResponseData(array $payload): array
    {
        if (! $this->hasColumn('paid_amount')) {
            $payload['paid_amount'] = 0;
        }

        if (! $this->hasColumn('frequency')) {
            $payload['frequency'] = 'One Time';
        }

        if (! $this->hasColumn('payroll_count')) {
            $payload['payroll_count'] = 1;
        }

        return $payload;
    }

    private function hasColumn(string $column): bool
    {
        return Schema::hasColumn((new PayrollAdjustment())->getTable(), $column);
    }
}
