<?php

namespace App\Http\Controllers\Api\CRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\CRM\StoreBusinessCustomerRequest;
use App\Models\CRM\Customer;
use App\Services\Business\CustomerService;
use Illuminate\Http\JsonResponse;

class BusinessCustomerController extends Controller
{
    public function __construct(private readonly CustomerService $customers)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => Customer::query()->with(['projects', 'sales', 'quotations', 'purchaseOrders', 'invoices', 'payments'])->paginate(15),
            'meta' => new \stdClass(),
        ]);
    }

    public function store(StoreBusinessCustomerRequest $request): JsonResponse
    {
        $customer = $this->customers->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Customer saved successfully.',
            'data' => $customer->load(['projects', 'sales', 'quotations', 'purchaseOrders', 'invoices', 'payments']),
            'meta' => new \stdClass(),
        ], 201);
    }
}
