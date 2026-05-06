<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\StoreEmployeeDeductionRequest;
use App\Models\HR\EmployeeDeduction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeductionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => EmployeeDeduction::query()
                ->when($request->filled('employee_id'), fn ($query) => $query->where('employee_id', $request->string('employee_id')))
                ->orderBy('type')
                ->get(),
        ]);
    }

    public function store(StoreEmployeeDeductionRequest $request): JsonResponse
    {
        $record = EmployeeDeduction::query()->create($request->validated());

        return response()->json([
            'message' => 'Deduction saved successfully.',
            'data' => $record,
        ], 201);
    }

    public function update(StoreEmployeeDeductionRequest $request, EmployeeDeduction $deduction): JsonResponse
    {
        $deduction->fill($request->validated())->save();

        return response()->json([
            'message' => 'Deduction updated successfully.',
            'data' => $deduction,
        ]);
    }
}
