<?php

namespace App\Http\Controllers\Api\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Assets\StoreAssetAssignmentRequest;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetAssignment;
use App\Services\Business\AssetService;
use Illuminate\Http\JsonResponse;

class AssetAssignmentController extends Controller
{
    public function __construct(private readonly AssetService $assets)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => AssetAssignment::query()->with('asset')->latest('assigned_date')->paginate(15),
            'meta' => new \stdClass(),
        ]);
    }

    public function assign(StoreAssetAssignmentRequest $request, Asset $asset): JsonResponse
    {
        $assignment = $this->assets->assign($asset, $request->validated(), 'Assigned');

        return response()->json([
            'success' => true,
            'message' => 'Asset assigned successfully.',
            'data' => $assignment->load('asset'),
            'meta' => new \stdClass(),
        ], 201);
    }

    public function borrow(StoreAssetAssignmentRequest $request, Asset $asset): JsonResponse
    {
        $assignment = $this->assets->assign($asset, $request->validated(), 'Borrowed');

        return response()->json([
            'success' => true,
            'message' => 'Asset borrowed successfully.',
            'data' => $assignment->load('asset'),
            'meta' => new \stdClass(),
        ], 201);
    }
}
