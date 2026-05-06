<?php

namespace App\Http\Controllers\Api\CRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\CRM\StoreInvoicePaymentRequest;
use App\Models\CRM\Payment;
use App\Services\Business\PaymentService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $payments)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => Payment::query()->with(['invoice', 'customer', 'sale'])->latest('payment_date')->paginate(15),
            'meta' => new \stdClass(),
        ]);
    }

    public function store(StoreInvoicePaymentRequest $request): JsonResponse
    {
        $payment = $this->payments->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Payment saved successfully.',
            'data' => $payment->load(['invoice', 'customer', 'sale']),
            'meta' => new \stdClass(),
        ], 201);
    }
}
