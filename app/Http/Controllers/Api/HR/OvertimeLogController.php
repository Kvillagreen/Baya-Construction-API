<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\StoreOvertimeLogRequest;
use App\Http\Requests\HR\UpdateOvertimeLogRequest;
use App\Models\HR\OvertimeLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OvertimeLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $logs = OvertimeLog::query()
            ->with('employee:id,employee_id,first_name,last_name')
            ->when($request->filled('employee_id'), fn ($query) => $query->where('employee_id', $request->string('employee_id')))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('date', '>=', $request->string('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('date', '<=', $request->string('date_to')))
            ->when($request->filled('approved'), fn ($query) => $query->where('approved', $request->boolean('approved')))
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (OvertimeLog $log) => [
                'id' => $log->id,
                'employee_id' => $log->employee_id,
                'date' => $log->date?->toDateString(),
                'hours' => (float) $log->hours,
                'approved' => (bool) $log->approved,
                'reason' => $log->reason,
                'employee' => $log->employee ? [
                    'id' => $log->employee->id,
                    'employee_id' => $log->employee->employee_id,
                    'first_name' => $log->employee->first_name,
                    'last_name' => $log->employee->last_name,
                ] : null,
            ]);

        return response()->json([
            'data' => $logs,
        ]);
    }

    public function store(StoreOvertimeLogRequest $request): JsonResponse
    {
        collect($request->validated()['logs'])
            ->each(fn (array $log) => OvertimeLog::query()->create([
                'employee_id' => $log['employee_id'],
                'date' => $log['date'],
                'hours' => $log['hours'],
                'approved' => $log['approved'],
                'reason' => $log['reason'] ?? null,
            ]));

        return response()->json([
            'message' => 'Overtime logs saved successfully.',
        ], 201);
    }

    public function update(UpdateOvertimeLogRequest $request, OvertimeLog $overtimeLog): JsonResponse
    {
        $overtimeLog->fill($request->validated())->save();

        return response()->json([
            'message' => 'Overtime log updated successfully.',
            'data' => $overtimeLog->load('employee:id,employee_id,first_name,last_name'),
        ]);
    }

    public function destroy(OvertimeLog $overtimeLog): JsonResponse
    {
        $overtimeLog->delete();

        return response()->json([
            'message' => 'Overtime log deleted successfully.',
        ]);
    }
}
