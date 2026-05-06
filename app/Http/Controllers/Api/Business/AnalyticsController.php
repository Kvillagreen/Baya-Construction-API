<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\Controller;
use App\Services\Business\AnalyticsService;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    public function __construct(private readonly AnalyticsService $analytics)
    {
    }

    public function crm(): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Success', 'data' => $this->analytics->customerSummary(), 'meta' => new \stdClass()]);
    }

    public function sales(): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Success', 'data' => $this->analytics->salesSummary(), 'meta' => new \stdClass()]);
    }

    public function inventory(): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Success', 'data' => $this->analytics->inventorySummary(), 'meta' => new \stdClass()]);
    }

    public function assets(): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Success', 'data' => $this->analytics->assetSummary(), 'meta' => new \stdClass()]);
    }
}
