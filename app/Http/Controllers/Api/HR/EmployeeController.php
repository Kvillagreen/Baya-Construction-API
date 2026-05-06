<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\StoreEmployeeRequest;
use App\Http\Requests\HR\UpdateEmployeeRequest;
use App\Models\HR\Employee;
use App\Support\FrontendApiPayload;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Employee::query()->with([
                'leaveBalances',
            ])->orderBy('last_name')
                ->paginate(15)
                ->through(fn (Employee $employee) => FrontendApiPayload::employee($employee)),
        ]);
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = Employee::query()->create($request->validated());

        return response()->json([
            'message' => 'Employee created successfully.',
            'data' => FrontendApiPayload::employee($employee->load('leaveBalances')),
        ], 201);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): JsonResponse
    {
        $employee->fill($request->validated())->save();

        return response()->json([
            'message' => 'Employee updated successfully.',
            'data' => FrontendApiPayload::employee($employee->load('leaveBalances')),
        ]);
    }

    public function destroy(Employee $employee): JsonResponse
    {
        $employee->delete();

        return response()->json([
            'message' => 'Employee deleted successfully.',
        ]);
    }
}
