<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Assets\Asset;
use App\Models\CRM\Customer;
use App\Models\HR\Employee;
use App\Models\Sales\Opportunity;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'data' => [
                'customers' => Customer::query()->count(),
                'employees' => Employee::query()->count(),
                'assets' => Asset::query()->count(),
                'sales_pipeline_total' => (float) Opportunity::query()->sum('amount'),
            ],
        ]);
    }
}
