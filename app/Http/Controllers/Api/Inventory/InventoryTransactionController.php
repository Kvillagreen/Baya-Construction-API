<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreInventoryTransactionRequest;
use App\Models\Inventory\InventoryItem;
use App\Models\Inventory\InventoryTransaction;
use App\Services\Business\InventoryService;
use Illuminate\Http\JsonResponse;

class InventoryTransactionController extends Controller
{
    public function __construct(private readonly InventoryService $inventory)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => InventoryTransaction::query()->with('item')->latest('transaction_date')->paginate(20),
            'meta' => new \stdClass(),
        ]);
    }

    public function store(StoreInventoryTransactionRequest $request): JsonResponse
    {
        $item = InventoryItem::query()->findOrFail($request->validated('item_id'));
        $transaction = $this->inventory->transact($item, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Inventory transaction saved successfully.',
            'data' => $transaction->load('item'),
            'meta' => new \stdClass(),
        ], 201);
    }
}
