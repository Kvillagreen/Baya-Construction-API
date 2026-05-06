<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreInventoryItemRequest;
use App\Http\Requests\Inventory\UpdateInventoryItemRequest;
use App\Models\Inventory\InventoryItem;
use App\Support\FrontendApiPayload;
use Illuminate\Http\JsonResponse;

class InventoryItemController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => InventoryItem::query()
                ->orderBy('item_name')
                ->paginate(20)
                ->through(fn (InventoryItem $item) => FrontendApiPayload::inventoryItem($item)),
        ]);
    }

    public function store(StoreInventoryItemRequest $request): JsonResponse
    {
        $item = InventoryItem::query()->create($request->validated());

        return response()->json([
            'message' => 'Inventory item created successfully.',
            'data' => FrontendApiPayload::inventoryItem($item),
        ], 201);
    }

    public function update(UpdateInventoryItemRequest $request, InventoryItem $inventoryItem): JsonResponse
    {
        $inventoryItem->fill($request->validated())->save();

        return response()->json([
            'message' => 'Inventory item updated successfully.',
            'data' => FrontendApiPayload::inventoryItem($inventoryItem),
        ]);
    }

    public function destroy(InventoryItem $inventoryItem): JsonResponse
    {
        $inventoryItem->delete();

        return response()->json([
            'message' => 'Inventory item deleted successfully.',
        ]);
    }
}
