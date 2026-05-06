<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\StoreLeaveEntryRequest;
use App\Http\Requests\HR\UpdateLeaveEntryRequest;
use App\Models\HR\LeaveEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaveEntryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $entries = LeaveEntry::query()
            ->with('employee:id,employee_id,first_name,last_name,position,department')
            ->when($request->filled('employee_id'), fn ($query) => $query->where('employee_id', $request->string('employee_id')))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('date_to', '>=', $request->string('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('date_from', '<=', $request->string('date_to')))
            ->latest('date_from')
            ->get()
            ->map(fn (LeaveEntry $entry) => [
                'id' => $entry->id,
                'employee_id' => $entry->employee_id,
                'leave_type' => $entry->leave_type,
                'date_from' => $entry->date_from?->toDateString(),
                'date_to' => $entry->date_to?->toDateString(),
                'reason' => $entry->reason,
                'employee' => $entry->employee ? [
                    'id' => $entry->employee->id,
                    'employee_id' => $entry->employee->employee_id,
                    'first_name' => $entry->employee->first_name,
                    'last_name' => $entry->employee->last_name,
                    'position' => $entry->employee->position,
                    'department' => $entry->employee->department,
                ] : null,
            ]);

        return response()->json([
            'data' => $entries,
        ]);
    }

    public function store(StoreLeaveEntryRequest $request): JsonResponse
    {
        $leave = LeaveEntry::query()->create($request->validated());

        return response()->json([
            'message' => 'Leave entry saved successfully.',
            'data' => $leave,
        ], 201);
    }

    public function update(UpdateLeaveEntryRequest $request, LeaveEntry $leaveEntry): JsonResponse
    {
        $leaveEntry->fill($request->validated())->save();

        return response()->json([
            'message' => 'Leave entry updated successfully.',
            'data' => $leaveEntry,
        ]);
    }

    public function destroy(LeaveEntry $leaveEntry): JsonResponse
    {
        $leaveEntry->delete();

        return response()->json([
            'message' => 'Leave entry deleted successfully.',
        ]);
    }
}
