<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\StorePayrollCutoffRefreshRequest;
use App\Models\HR\PayrollCutoffRefresh;
use Illuminate\Http\JsonResponse;

class PayrollCutoffController extends Controller
{
    public function latest(): JsonResponse
    {
        return response()->json([
            'data' => PayrollCutoffRefresh::query()->latest('refreshed_at')->first(),
        ]);
    }

    public function store(StorePayrollCutoffRefreshRequest $request): JsonResponse
    {
        $cutoff = PayrollCutoffRefresh::query()->create([
            ...$request->validated(),
            'refreshed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Payroll cutoff refreshed successfully.',
            'data' => $cutoff,
        ], 201);
    }
}
