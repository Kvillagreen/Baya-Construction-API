<?php

namespace App\Http\Controllers\Api\CRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\CRM\StoreQuotationDocumentRequest;
use App\Models\CRM\Quotation;
use App\Services\Business\QuotationService;
use Illuminate\Http\JsonResponse;

class QuotationDocumentController extends Controller
{
    public function __construct(private readonly QuotationService $quotations)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => Quotation::query()->with(['customer', 'project', 'sale', 'items'])->paginate(15),
            'meta' => new \stdClass(),
        ]);
    }

    public function store(StoreQuotationDocumentRequest $request): JsonResponse
    {
        $quotation = $this->quotations->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Quotation saved successfully.',
            'data' => $quotation,
            'meta' => new \stdClass(),
        ], 201);
    }

    public function accept(Quotation $quotation): JsonResponse
    {
        $quotation = $this->quotations->accept($quotation);

        return response()->json([
            'success' => true,
            'message' => 'Quotation accepted successfully.',
            'data' => $quotation,
            'meta' => new \stdClass(),
        ]);
    }
}
