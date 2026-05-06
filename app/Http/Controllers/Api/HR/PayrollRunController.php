<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\FinalizePayrollRunRequest;
use App\Http\Requests\HR\PreviewPayrollRunRequest;
use App\Models\HR\PayrollPeriod;
use App\Models\HR\PayrollRun;
use App\Services\HR\PayrollCalculationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayrollRunController extends Controller
{
    public function __construct(private readonly PayrollCalculationService $payrolls)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $runs = PayrollRun::query()
            ->with(['employee', 'payrollPeriod', 'items', 'payslip'])
            ->when($request->filled('payroll_period_id'), fn ($query) => $query->where('payroll_period_id', $request->string('payroll_period_id')))
            ->when($request->filled('employee_id'), fn ($query) => $query->where('employee_id', $request->string('employee_id')))
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (PayrollRun $run) => $this->payrolls->formatRun($run))
            ->values();

        return response()->json(['data' => $runs]);
    }

    public function show(PayrollRun $payrollRun): JsonResponse
    {
        return response()->json([
            'data' => $this->payrolls->formatRun($payrollRun),
        ]);
    }

    public function preview(PreviewPayrollRunRequest $request): JsonResponse
    {
        $period = PayrollPeriod::query()->findOrFail($request->validated('payroll_period_id'));

        return response()->json([
            'data' => $this->payrolls->preview($period, $request->validated('employee_id')),
        ]);
    }

    public function finalize(FinalizePayrollRunRequest $request): JsonResponse
    {
        $period = PayrollPeriod::query()->findOrFail($request->validated('payroll_period_id'));

        return response()->json([
            'message' => 'Payroll finalized successfully.',
            'data' => $this->payrolls->finalize($period, $request->validated('employee_id')),
        ]);
    }
}
