<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\StoreDailyTimeRecordRequest;
use App\Models\HR\DailyTimeRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DailyTimeRecordController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $records = DailyTimeRecord::query()
            ->with('employee:id,employee_id,first_name,last_name,position,department')
            ->when($request->filled('employee_id'), fn ($query) => $query->where('employee_id', $request->string('employee_id')))
            ->when($request->filled('cutoff'), fn ($query) => $query->where('cutoff', $request->string('cutoff')))
            ->when($request->filled('period_from'), fn ($query) => $query->whereDate('date', '>=', $request->string('period_from')))
            ->when($request->filled('period_to'), fn ($query) => $query->whereDate('date', '<=', $request->string('period_to')))
            ->orderBy('employee_id')
            ->orderBy('date')
            ->get()
            ->map(fn (DailyTimeRecord $record) => [
                'id' => $record->id,
                'employee_id' => $record->employee_id,
                'date' => $record->date?->toDateString(),
                'cutoff' => $record->cutoff,
                'period_from' => $record->period_from?->toDateString(),
                'period_to' => $record->period_to?->toDateString(),
                'day_type' => $record->day_type,
                'attendance' => $record->attendance,
                'leave_type' => $record->leave_type,
                'overtime_hours' => (float) $record->overtime_hours,
                'employee' => $record->employee ? [
                    'id' => $record->employee->id,
                    'employee_id' => $record->employee->employee_id,
                    'first_name' => $record->employee->first_name,
                    'last_name' => $record->employee->last_name,
                    'position' => $record->employee->position,
                    'department' => $record->employee->department,
                ] : null,
            ]);

        return response()->json([
            'data' => $records,
        ]);
    }

    public function store(StoreDailyTimeRecordRequest $request): JsonResponse
    {
        foreach ($request->validated('records') as $record) {
            DailyTimeRecord::query()->updateOrCreate(
                [
                    'employee_id' => $record['employee_id'],
                    'date' => $record['date'],
                ],
                [
                    'cutoff' => $record['cutoff'],
                    'period_from' => $record['period_from'],
                    'period_to' => $record['period_to'],
                    'day_type' => $record['day_type'],
                    'attendance' => $record['attendance'],
                    'leave_type' => $record['leave_type'] ?? null,
                    'overtime_hours' => $record['overtime_hours'] ?? 0,
                ]
            );
        }

        return response()->json([
            'message' => 'DTR records saved successfully.',
        ], 201);
    }
}
