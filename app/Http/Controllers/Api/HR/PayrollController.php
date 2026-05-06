<?php

namespace App\Http\Controllers\Api\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Employee;
use Illuminate\Http\JsonResponse;

class PayrollController extends Controller
{
    public function index(): JsonResponse
    {
        $employees = Employee::query()->get()->map(function (Employee $employee): array {
            $baseSalary = (float) $employee->base_salary_encrypted;
            $dailyRate = (float) ($employee->daily_rate ?: round($baseSalary / 26, 2));
            $sss = (float) $employee->sss_contribution;
            $philhealth = (float) $employee->philhealth_contribution;
            $pagibig = (float) $employee->pagibig_contribution;
            $withholdingTax = (float) $employee->withholding_tax;
            $net = $baseSalary - ($sss + $philhealth + $pagibig + $withholdingTax);

            return [
                'employee_id' => $employee->employee_id,
                'name' => $employee->first_name.' '.$employee->last_name,
                'salary_type' => $employee->salary_type,
                'daily_rate' => round($dailyRate, 2),
                'gross_pay' => $baseSalary,
                'sss' => round($sss, 2),
                'philhealth' => round($philhealth, 2),
                'pagibig' => round($pagibig, 2),
                'withholding_tax' => round($withholdingTax, 2),
                'net_pay' => round($net, 2),
                'thirteenth_month_accrual' => round($baseSalary / 12, 2),
            ];
        });

        return response()->json([
            'data' => $employees,
        ]);
    }
}
