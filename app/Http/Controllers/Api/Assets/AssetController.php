<?php

namespace App\Http\Controllers\Api\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Assets\StoreAssetRequest;
use App\Http\Requests\Assets\UpdateAssetRequest;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetCategory;
use App\Support\FrontendApiPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Asset::query()->with(['category', 'assignedEmployee']);

        $query->when($request->string('category')->isNotEmpty(), function ($builder) use ($request): void {
            $builder->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', $request->string('category')));
        });

        return response()->json([
            'data' => $query->paginate(15)->through(fn (Asset $asset) => FrontendApiPayload::asset($asset)),
        ]);
    }

    public function store(StoreAssetRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $asset = Asset::query()->create([
            'asset_category_id' => $this->resolveCategoryId($payload['category']),
            'asset_name' => $payload['asset_name'],
            'asset_stocks' => $payload['asset_stocks'],
            'model_serial_number' => $payload['model_serial_number'],
            'asset_acquired_date' => $payload['asset_acquired_date'] ?? null,
            'asset_price_encrypted' => $payload['asset_price_encrypted'],
            'assigned_employee_id' => $payload['assigned_employee_id'] ?? null,
            'status' => $payload['status'],
        ]);

        return response()->json([
            'message' => 'Asset created successfully.',
            'data' => FrontendApiPayload::asset($asset->load(['category', 'assignedEmployee'])),
        ], 201);
    }

    public function update(UpdateAssetRequest $request, Asset $asset): JsonResponse
    {
        $payload = $request->validated();

        if (array_key_exists('category', $payload)) {
            $payload['asset_category_id'] = $this->resolveCategoryId($payload['category']);
            unset($payload['category']);
        }

        $asset->fill($payload)->save();

        return response()->json([
            'message' => 'Asset updated successfully.',
            'data' => FrontendApiPayload::asset($asset->load(['category', 'assignedEmployee'])),
        ]);
    }

    public function destroy(Asset $asset): JsonResponse
    {
        $asset->delete();

        return response()->json([
            'message' => 'Asset deleted successfully.',
        ]);
    }

    private function resolveCategoryId(string $categoryName): string
    {
        return AssetCategory::query()->firstOrCreate(
            ['name' => trim($categoryName)],
            ['description' => trim($categoryName).' assets']
        )->id;
    }
}
