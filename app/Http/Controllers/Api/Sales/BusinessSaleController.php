<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\StoreBusinessSaleRequest;
use App\Models\Sales\Opportunity;
use App\Services\Business\SalesService;
use Illuminate\Http\JsonResponse;

class BusinessSaleController extends Controller
{
    public function __construct(private readonly SalesService $sales)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => Opportunity::query()->with(['customer', 'projectRecord', 'quotation', 'purchaseOrders', 'invoices', 'logs'])->paginate(15),
            'meta' => new \stdClass(),
        ]);
    }

    public function store(StoreBusinessSaleRequest $request): JsonResponse
    {
        $sale = $this->sales->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Sale saved successfully.',
            'data' => $sale->load(['customer', 'projectRecord', 'quotation', 'purchaseOrders', 'invoices', 'logs']),
            'meta' => new \stdClass(),
        ], 201);
    }
}
