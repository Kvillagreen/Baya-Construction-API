<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\PayrollRun;
use App\Models\HR\Payslip;
use App\Services\HR\PayrollCalculationService;
use Illuminate\Http\JsonResponse;

class PayslipController extends Controller
{
    public function __construct(private readonly PayrollCalculationService $payrolls)
    {
    }

    public function generate(PayrollRun $payrollRun): JsonResponse
    {
        return response()->json([
            'message' => 'Payslip generated successfully.',
            'data' => $this->payrolls->generatePayslip($payrollRun),
        ]);
    }

    public function show(Payslip $payslip): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $payslip->id,
                'payrollRunId' => $payslip->payroll_run_id,
                'employeeId' => $payslip->employee_id,
                'payslipNumber' => $payslip->payslip_number,
                'generatedAt' => optional($payslip->generated_at)->toIso8601String(),
                'payload' => $payslip->payload,
            ],
        ]);
    }
}
