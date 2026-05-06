<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\StoreOpportunityRequest;
use App\Http\Requests\Sales\UpdateOpportunityRequest;
use App\Models\Sales\Opportunity;
use App\Support\FrontendApiPayload;
use Illuminate\Http\JsonResponse;

class OpportunityController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Opportunity::query()
                ->orderBy('account_name')
                ->paginate(15)
                ->through(fn (Opportunity $opportunity) => FrontendApiPayload::opportunity($opportunity)),
        ]);
    }

    public function store(StoreOpportunityRequest $request): JsonResponse
    {
        $opportunity = Opportunity::query()->create($request->validated());

        return response()->json([
            'message' => 'Opportunity created successfully.',
            'data' => FrontendApiPayload::opportunity($opportunity),
        ], 201);
    }

    public function update(UpdateOpportunityRequest $request, Opportunity $opportunity): JsonResponse
    {
        $opportunity->fill($request->validated())->save();

        return response()->json([
            'message' => 'Opportunity updated successfully.',
            'data' => FrontendApiPayload::opportunity($opportunity),
        ]);
    }

    public function destroy(Opportunity $opportunity): JsonResponse
    {
        $opportunity->delete();

        return response()->json([
            'message' => 'Opportunity deleted successfully.',
        ]);
    }
}
