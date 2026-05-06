<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\StorePayrollPeriodRequest;
use App\Models\HR\PayrollPeriod;
use Illuminate\Http\JsonResponse;

class PayrollPeriodController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => PayrollPeriod::query()->orderByDesc('date_from')->get(),
        ]);
    }

    public function store(StorePayrollPeriodRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $period = PayrollPeriod::query()->updateOrCreate(
            [
                'cutoff' => $validated['cutoff'],
                'date_from' => $validated['date_from'],
                'date_to' => $validated['date_to'],
            ],
            [
                'name' => $validated['name'] ?? $validated['cutoff'].' '.date('M d, Y', strtotime($validated['date_from'])).' - '.date('M d, Y', strtotime($validated['date_to'])),
                'status' => $validated['status'] ?? 'Open',
            ]
        );

        return response()->json([
            'message' => 'Payroll period saved successfully.',
            'data' => $period,
        ], 201);
    }
}
