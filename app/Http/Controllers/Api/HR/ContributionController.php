<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\StoreEmployeeContributionRequest;
use App\Models\HR\EmployeeContribution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContributionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => EmployeeContribution::query()
                ->when($request->filled('employee_id'), fn ($query) => $query->where('employee_id', $request->string('employee_id')))
                ->orderBy('type')
                ->get(),
        ]);
    }

    public function store(StoreEmployeeContributionRequest $request): JsonResponse
    {
        $record = EmployeeContribution::query()->create($request->validated());

        return response()->json([
            'message' => 'Contribution saved successfully.',
            'data' => $record,
        ], 201);
    }

    public function update(StoreEmployeeContributionRequest $request, EmployeeContribution $contribution): JsonResponse
    {
        $contribution->fill($request->validated())->save();

        return response()->json([
            'message' => 'Contribution updated successfully.',
            'data' => $contribution,
        ]);
    }
}
