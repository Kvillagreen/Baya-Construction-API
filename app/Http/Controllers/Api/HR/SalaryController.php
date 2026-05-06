<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\StoreEmployeeSalaryRequest;
use App\Models\HR\EmployeeSalary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => EmployeeSalary::query()
                ->when($request->filled('employee_id'), fn ($query) => $query->where('employee_id', $request->string('employee_id')))
                ->orderByDesc('effective_from')
                ->get(),
        ]);
    }

    public function store(StoreEmployeeSalaryRequest $request): JsonResponse
    {
        $salary = EmployeeSalary::query()->create($request->validated());

        return response()->json([
            'message' => 'Employee salary saved successfully.',
            'data' => $salary,
        ], 201);
    }

    public function update(StoreEmployeeSalaryRequest $request, EmployeeSalary $salary): JsonResponse
    {
        $salary->fill($request->validated())->save();

        return response()->json([
            'message' => 'Employee salary updated successfully.',
            'data' => $salary,
        ]);
    }
}
