<?php

namespace App\Services\HR;

use App\Models\HR\DailyTimeRecord;
use App\Models\HR\Employee;
use App\Models\HR\EmployeeContribution;
use App\Models\HR\EmployeeDeduction;
use App\Models\HR\EmployeeSalary;
use App\Models\HR\LeaveEntry;
use App\Models\HR\OvertimeLog;
use App\Models\HR\PayrollAdjustment;
use App\Models\HR\PayrollItem;
use App\Models\HR\PayrollPeriod;
use App\Models\HR\PayrollRun;
use App\Models\HR\Payslip;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PayrollCalculationService
{
    public function preview(PayrollPeriod $period, ?string $employeeId = null): array
    {
        return $this->employeesForPeriod($employeeId)->map(fn (Employee $employee) => $this->buildRunPayload($employee, $period))->values()->all();
    }

    public function finalize(PayrollPeriod $period, ?string $employeeId = null): array
    {
        return DB::transaction(function () use ($period, $employeeId): array {
            $runs = collect($this->preview($period, $employeeId))->map(function (array $payload) use ($period) {
                /** @var PayrollRun $run */
                $run = PayrollRun::query()->updateOrCreate(
                    [
                        'payroll_period_id' => $period->id,
                        'employee_id' => $payload['employeeKey'],
                    ],
                    [
                        'status' => 'Finalized',
                        'salary_snapshot' => $payload['snapshots']['salary'],
                        'attendance_snapshot' => $payload['snapshots']['attendance'],
                        'leave_snapshot' => $payload['snapshots']['leave'],
                        'overtime_snapshot' => $payload['snapshots']['overtime'],
                        'payable_days' => $payload['payableDays'],
                        'days_present' => $payload['daysPresent'],
                        'paid_leave_days' => $payload['paidLeaveDays'],
                        'unpaid_leave_days' => $payload['unpaidLeaveDays'],
                        'regular_hours' => $payload['regularHours'],
                        'overtime_hours' => $payload['overtimeHours'],
                        'regular_pay' => $payload['regularPay'],
                        'paid_leave_pay' => $payload['paidLeavePay'],
                        'overtime_pay' => $payload['overtimePay'],
                        'gross_pay' => $payload['grossPay'],
                        'total_contributions' => $payload['totalContributions'],
                        'total_deductions' => $payload['totalDeductions'],
                        'net_pay' => $payload['netPay'],
                        'finalized_at' => now(),
                    ]
                );

                $run->items()->delete();
                $run->items()->createMany(collect($payload['items'])->map(fn (array $item) => [
                    'category' => $item['category'],
                    'code' => $item['code'],
                    'label' => $item['label'],
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'amount' => $item['amount'],
                    'meta' => $item['meta'] ?? null,
                ])->all());

                return $this->formatRun($run->fresh(['employee', 'payrollPeriod', 'items', 'payslip']));
            })->values()->all();

            $period->update([
                'status' => 'Finalized',
                'locked_at' => now(),
            ]);

            return $runs;
        });
    }

    public function generatePayslip(PayrollRun $run): array
    {
        $run->loadMissing(['employee', 'payrollPeriod', 'items', 'payslip']);

        $payload = [
            'employeeId' => $run->employee->employee_id,
            'employeeName' => trim($run->employee->first_name.' '.$run->employee->last_name),
            'position' => $run->employee->position,
            'department' => $run->employee->department,
            'payrollPeriod' => $run->payrollPeriod->date_from->toDateString().' to '.$run->payrollPeriod->date_to->toDateString(),
            'cutoff' => $run->payrollPeriod->cutoff,
            'dailyRate' => (float) $run->salary_snapshot['dailyRate'],
            'hourlyRate' => (float) $run->salary_snapshot['hourlyRate'],
            'payableDays' => (float) $run->payable_days,
            'daysPresent' => (float) $run->days_present,
            'paidLeaveDays' => (float) $run->paid_leave_days,
            'unpaidLeaveDays' => (float) $run->unpaid_leave_days,
            'overtimeHours' => (float) $run->overtime_hours,
            'regularPay' => (float) $run->regular_pay,
            'paidLeavePay' => (float) $run->paid_leave_pay,
            'overtimePay' => (float) $run->overtime_pay,
            'grossPay' => (float) $run->gross_pay,
            'totalContributions' => (float) $run->total_contributions,
            'totalDeductions' => (float) $run->total_deductions,
            'netPay' => (float) $run->net_pay,
            'items' => $run->items->map(fn (PayrollItem $item) => [
                'category' => $item->category,
                'code' => $item->code,
                'label' => $item->label,
                'quantity' => (float) $item->quantity,
                'rate' => (float) $item->rate,
                'amount' => (float) $item->amount,
                'meta' => $item->meta ?? [],
            ])->values()->all(),
        ];

        $payslip = Payslip::query()->updateOrCreate(
            ['payroll_run_id' => $run->id],
            [
                'employee_id' => $run->employee_id,
                'payslip_number' => $run->payslip?->payslip_number ?? $this->makePayslipNumber($run),
                'payload' => $payload,
                'generated_at' => now(),
            ]
        );

        return [
            'id' => $payslip->id,
            'payrollRunId' => $run->id,
            'payslipNumber' => $payslip->payslip_number,
            'generatedAt' => optional($payslip->generated_at)->toIso8601String(),
            'payload' => $payload,
        ];
    }

    public function formatRun(PayrollRun $run): array
    {
        $run->loadMissing(['employee', 'payrollPeriod', 'items', 'payslip']);

        return [
            'id' => $run->id,
            'employeeKey' => $run->employee_id,
            'employeeId' => $run->employee->employee_id,
            'employeeName' => trim($run->employee->first_name.' '.$run->employee->last_name),
            'position' => $run->employee->position,
            'department' => $run->employee->department,
            'payrollPeriodId' => $run->payroll_period_id,
            'payrollPeriod' => $run->payrollPeriod->date_from->toDateString().' to '.$run->payrollPeriod->date_to->toDateString(),
            'cutoff' => $run->payrollPeriod->cutoff,
            'status' => $run->status,
            'dailyRate' => (float) ($run->salary_snapshot['dailyRate'] ?? 0),
            'hourlyRate' => (float) ($run->salary_snapshot['hourlyRate'] ?? 0),
            'payableDays' => (float) $run->payable_days,
            'daysPresent' => (float) $run->days_present,
            'paidLeaveDays' => (float) $run->paid_leave_days,
            'unpaidLeaveDays' => (float) $run->unpaid_leave_days,
            'regularHours' => (float) $run->regular_hours,
            'overtimeHours' => (float) $run->overtime_hours,
            'regularPay' => (float) $run->regular_pay,
            'paidLeavePay' => (float) $run->paid_leave_pay,
            'overtimePay' => (float) $run->overtime_pay,
            'grossPay' => (float) $run->gross_pay,
            'totalContributions' => (float) $run->total_contributions,
            'totalDeductions' => (float) $run->total_deductions,
            'netPay' => (float) $run->net_pay,
            'items' => $run->items->map(fn (PayrollItem $item) => [
                'category' => $item->category,
                'code' => $item->code,
                'label' => $item->label,
                'quantity' => (float) $item->quantity,
                'rate' => (float) $item->rate,
                'amount' => (float) $item->amount,
                'meta' => $item->meta ?? [],
            ])->values()->all(),
            'payslip' => $run->payslip ? [
                'id' => $run->payslip->id,
                'payslipNumber' => $run->payslip->payslip_number,
                'generatedAt' => optional($run->payslip->generated_at)->toIso8601String(),
            ] : null,
        ];
    }

    private function buildRunPayload(Employee $employee, PayrollPeriod $period): array
    {
        $salary = $this->activeSalary($employee, $period);
        $dailyRate = $this->money($salary['dailyRate']);
        $hourlyRate = $this->money($salary['hourlyRate']);
        $overtimeRate = $this->money($salary['overtimeRate'] ?: ($hourlyRate * 1.25));

        $attendance = DailyTimeRecord::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$period->date_from, $period->date_to])
            ->orderBy('date')
            ->get();

        $leaves = LeaveEntry::query()
            ->where('employee_id', $employee->id)
            ->whereDate('date_from', '<=', $period->date_to)
            ->whereDate('date_to', '>=', $period->date_from)
            ->orderBy('date_from')
            ->get();

        $overtime = OvertimeLog::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$period->date_from, $period->date_to])
            ->where('approved', true)
            ->orderBy('date')
            ->get();

        $contributions = $this->activeContributions($employee, $period);
        $deductions = $this->activeDeductions($employee, $period);
        $adjustments = PayrollAdjustment::query()->where('employee_id', $employee->id)->get();

        $daysPresent = $attendance->where('attendance', 'Present')->count();
        $paidLeaveDays = $this->countLeaveDays($leaves, false);
        $unpaidLeaveDays = $this->countLeaveDays($leaves, true);
        $payableDays = $daysPresent + $paidLeaveDays;
        $regularHours = $attendance->filter(fn (DailyTimeRecord $record) => $record->attendance !== 'Absent')->count() * 8;
        $overtimeHours = $this->money($overtime->sum('hours') ?: $attendance->sum('overtime_hours'));

        $regularPay = $this->money($daysPresent * $dailyRate);
        $paidLeavePay = $this->money($paidLeaveDays * $dailyRate);
        $overtimePay = $this->money($overtimeHours * $overtimeRate);

        $earningAdjustments = $this->money($adjustments->where('type', 'Earning')->sum(fn (PayrollAdjustment $item) => $this->normalizedAdjustmentAmount($item)));
        $contributionAdjustments = $this->money($adjustments->where('type', 'Contribution')->sum(fn (PayrollAdjustment $item) => $this->normalizedAdjustmentAmount($item)));
        $deductionAdjustments = $this->money($adjustments->where('type', 'Deduction')->sum(fn (PayrollAdjustment $item) => $this->normalizedAdjustmentAmount($item)));
        $totalContributions = $this->money($contributions->sum('employee_share') + $contributionAdjustments);
        $totalDeductions = $this->money($deductions->sum('amount') + $deductionAdjustments);
        $grossPay = $this->money($regularPay + $paidLeavePay + $overtimePay + $earningAdjustments);
        $netPay = $this->money($grossPay - $totalContributions - $totalDeductions);

        return [
            'employeeKey' => $employee->id,
            'employeeId' => $employee->employee_id,
            'employeeName' => trim($employee->first_name.' '.$employee->last_name),
            'position' => $employee->position,
            'department' => $employee->department,
            'payrollPeriodId' => $period->id,
            'payrollPeriod' => $period->date_from->toDateString().' to '.$period->date_to->toDateString(),
            'cutoff' => $period->cutoff,
            'status' => 'Preview',
            'dailyRate' => $dailyRate,
            'hourlyRate' => $hourlyRate,
            'payableDays' => $payableDays,
            'daysPresent' => $daysPresent,
            'paidLeaveDays' => $paidLeaveDays,
            'unpaidLeaveDays' => $unpaidLeaveDays,
            'regularHours' => $regularHours,
            'overtimeHours' => $overtimeHours,
            'regularPay' => $regularPay,
            'paidLeavePay' => $paidLeavePay,
            'overtimePay' => $overtimePay,
            'grossPay' => $grossPay,
            'totalContributions' => $totalContributions,
            'totalDeductions' => $totalDeductions,
            'netPay' => $netPay,
            'items' => array_merge(
                $this->earningItems($daysPresent, $dailyRate, $paidLeaveDays, $overtimeHours, $overtimeRate, $adjustments),
                $contributions->map(fn (EmployeeContribution $item) => [
                    'category' => 'contribution',
                    'code' => Str::slug($item->type, '_'),
                    'label' => $item->label,
                    'quantity' => 1,
                    'rate' => (float) $item->employee_share,
                    'amount' => (float) $item->employee_share,
                ])->values()->all(),
                $deductions->map(fn (EmployeeDeduction $item) => [
                    'category' => 'deduction',
                    'code' => Str::slug($item->type, '_'),
                    'label' => $item->label,
                    'quantity' => 1,
                    'rate' => (float) $item->amount,
                    'amount' => (float) $item->amount,
                ])->values()->all(),
                $this->adjustmentItems($adjustments)
            ),
            'snapshots' => [
                'salary' => $salary,
                'attendance' => $attendance->map(fn (DailyTimeRecord $record) => [
                    'date' => $record->date->toDateString(),
                    'attendance' => $record->attendance,
                    'dayType' => $record->day_type,
                    'leaveType' => $record->leave_type,
                    'overtimeHours' => (float) $record->overtime_hours,
                ])->values()->all(),
                'leave' => $leaves->map(fn (LeaveEntry $record) => [
                    'leaveType' => $record->leave_type,
                    'dateFrom' => $record->date_from->toDateString(),
                    'dateTo' => $record->date_to->toDateString(),
                    'reason' => $record->reason,
                ])->values()->all(),
                'overtime' => $overtime->map(fn (OvertimeLog $record) => [
                    'date' => $record->date->toDateString(),
                    'hours' => (float) $record->hours,
                    'reason' => $record->reason,
                ])->values()->all(),
            ],
        ];
    }

    private function employeesForPeriod(?string $employeeId = null): Collection
    {
        return Employee::query()
            ->when($employeeId, fn ($query) => $query->whereKey($employeeId))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    private function activeSalary(Employee $employee, PayrollPeriod $period): array
    {
        $record = EmployeeSalary::query()
            ->where('employee_id', $employee->id)
            ->where('is_active', true)
            ->whereDate('effective_from', '<=', $period->date_to)
            ->where(function ($query) use ($period): void {
                $query->whereNull('effective_to')->orWhereDate('effective_to', '>=', $period->date_from);
            })
            ->orderByDesc('effective_from')
            ->first();

        $monthlyRate = $record ? (float) $record->monthly_rate : (float) $employee->base_salary_encrypted;
        $dailyRate = $record ? (float) $record->daily_rate : (float) ($employee->daily_rate ?: round($monthlyRate / 26, 2));
        $hourlyRate = $record ? (float) $record->hourly_rate : round($dailyRate / 8, 2);
        $overtimeRate = $record ? (float) $record->overtime_rate : round($hourlyRate * 1.25, 2);

        return [
            'salaryType' => $record?->salary_type ?? $employee->salary_type,
            'monthlyRate' => $this->money($monthlyRate),
            'dailyRate' => $this->money($dailyRate),
            'hourlyRate' => $this->money($hourlyRate),
            'overtimeRate' => $this->money($overtimeRate),
            'source' => $record ? 'employee_salaries' : 'employees',
        ];
    }

    private function activeContributions(Employee $employee, PayrollPeriod $period): Collection
    {
        $records = EmployeeContribution::query()
            ->where('employee_id', $employee->id)
            ->where('is_active', true)
            ->where(function ($query) use ($period): void {
                $query->whereNull('effective_from')->orWhereDate('effective_from', '<=', $period->date_to);
            })
            ->where(function ($query) use ($period): void {
                $query->whereNull('effective_to')->orWhereDate('effective_to', '>=', $period->date_from);
            })
            ->get();

        if ($records->isNotEmpty()) {
            return $records;
        }

        return collect([
            ['type' => 'SSS', 'label' => 'SSS', 'employee_share' => (float) $employee->sss_contribution],
            ['type' => 'PhilHealth', 'label' => 'PhilHealth', 'employee_share' => (float) $employee->philhealth_contribution],
            ['type' => 'Pag-IBIG', 'label' => 'Pag-IBIG', 'employee_share' => (float) $employee->pagibig_contribution],
            ['type' => 'Withholding Tax', 'label' => 'Withholding Tax', 'employee_share' => (float) $employee->withholding_tax],
        ])->filter(fn (array $entry) => $entry['employee_share'] > 0)->map(fn (array $entry) => new EmployeeContribution($entry));
    }

    private function activeDeductions(Employee $employee, PayrollPeriod $period): Collection
    {
        return EmployeeDeduction::query()
            ->where('employee_id', $employee->id)
            ->where('is_active', true)
            ->where(function ($query) use ($period): void {
                $query->whereNull('effective_from')->orWhereDate('effective_from', '<=', $period->date_to);
            })
            ->where(function ($query) use ($period): void {
                $query->whereNull('effective_to')->orWhereDate('effective_to', '>=', $period->date_from);
            })
            ->get()
            ->map(function (EmployeeDeduction $item) {
                $amount = $item->frequency === 'Every Payroll' && $item->remaining_periods > 1
                    ? $this->money((float) $item->amount / $item->remaining_periods)
                    : (float) $item->amount;

                $item->amount = $amount;

                return $item;
            });
    }

    private function countLeaveDays(Collection $leaves, bool $unpaid): float
    {
        $days = 0;

        foreach ($leaves as $leave) {
            $isUnpaid = $leave->leave_type === 'Unpaid Leave';

            if ($unpaid !== $isUnpaid) {
                continue;
            }

            $days += $leave->date_from->diffInDays($leave->date_to) + 1;
        }

        return (float) $days;
    }

    private function earningItems(float $daysPresent, float $dailyRate, float $paidLeaveDays, float $overtimeHours, float $overtimeRate, Collection $adjustments): array
    {
        $items = [
            [
                'category' => 'earning',
                'code' => 'regular_pay',
                'label' => 'Regular Pay',
                'quantity' => $daysPresent,
                'rate' => $dailyRate,
                'amount' => $this->money($daysPresent * $dailyRate),
            ],
        ];

        if ($paidLeaveDays > 0) {
            $items[] = [
                'category' => 'earning',
                'code' => 'paid_leave_pay',
                'label' => 'Paid Leave Pay',
                'quantity' => $paidLeaveDays,
                'rate' => $dailyRate,
                'amount' => $this->money($paidLeaveDays * $dailyRate),
            ];
        }

        if ($overtimeHours > 0) {
            $items[] = [
                'category' => 'earning',
                'code' => 'overtime_pay',
                'label' => 'Overtime Pay',
                'quantity' => $overtimeHours,
                'rate' => $overtimeRate,
                'amount' => $this->money($overtimeHours * $overtimeRate),
            ];
        }

        foreach ($adjustments->where('type', 'Earning') as $adjustment) {
            $items[] = [
                'category' => 'earning',
                'code' => 'adjustment_'.Str::slug($adjustment->label, '_'),
                'label' => $adjustment->label,
                'quantity' => 1,
                'rate' => $this->normalizedAdjustmentAmount($adjustment),
                'amount' => $this->normalizedAdjustmentAmount($adjustment),
                'meta' => ['source' => 'payroll_adjustments'],
            ];
        }

        return $items;
    }

    private function adjustmentItems(Collection $adjustments): array
    {
        return $adjustments
            ->whereIn('type', ['Contribution', 'Deduction'])
            ->map(fn (PayrollAdjustment $adjustment) => [
                'category' => strtolower($adjustment->type),
                'code' => 'adjustment_'.Str::slug($adjustment->label, '_'),
                'label' => $adjustment->label,
                'quantity' => 1,
                'rate' => $this->normalizedAdjustmentAmount($adjustment),
                'amount' => $this->normalizedAdjustmentAmount($adjustment),
                'meta' => ['source' => 'payroll_adjustments'],
            ])
            ->values()
            ->all();
    }

    private function normalizedAdjustmentAmount(PayrollAdjustment $adjustment): float
    {
        $amount = (float) $adjustment->amount;

        if ($adjustment->frequency === 'Every Payroll' && $adjustment->payroll_count > 1) {
            return $this->money($amount / $adjustment->payroll_count);
        }

        return $this->money($amount);
    }

    private function makePayslipNumber(PayrollRun $run): string
    {
        return sprintf(
            'PS-%s-%s',
            now()->format('Ymd'),
            strtoupper(substr(str_replace('-', '', $run->employee->employee_id), -6))
        );
    }

    private function money(float $value): float
    {
        return round($value, 2);
    }
}
