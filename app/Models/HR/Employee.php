<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Employee extends Model
{
    use HasUlids, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'position',
        'date_hired',
        'department',
        'employment_status',
        'salary_type',
        'base_salary_encrypted',
        'daily_rate',
        'sss_contribution',
        'philhealth_contribution',
        'pagibig_contribution',
        'withholding_tax',
    ];

    protected function casts(): array
    {
        return [
            'date_hired' => 'date',
            'base_salary_encrypted' => 'encrypted',
            'daily_rate' => 'decimal:2',
            'sss_contribution' => 'decimal:2',
            'philhealth_contribution' => 'decimal:2',
            'pagibig_contribution' => 'decimal:2',
            'withholding_tax' => 'decimal:2',
        ];
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(EmployeeLeaveBalance::class);
    }

    public function dailyTimeRecords(): HasMany
    {
        return $this->hasMany(DailyTimeRecord::class);
    }

    public function leaveEntries(): HasMany
    {
        return $this->hasMany(LeaveEntry::class);
    }

    public function salaryRecords(): HasMany
    {
        return $this->hasMany(EmployeeSalary::class);
    }

    public function contributionRecords(): HasMany
    {
        return $this->hasMany(EmployeeContribution::class);
    }

    public function deductionRecords(): HasMany
    {
        return $this->hasMany(EmployeeDeduction::class);
    }

    public function overtimeLogs(): HasMany
    {
        return $this->hasMany(OvertimeLog::class);
    }

    public function payrollRuns(): HasMany
    {
        return $this->hasMany(PayrollRun::class);
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }
}
