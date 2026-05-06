<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PayrollRun extends Model
{
    use HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'status',
        'salary_snapshot',
        'attendance_snapshot',
        'leave_snapshot',
        'overtime_snapshot',
        'payable_days',
        'days_present',
        'paid_leave_days',
        'unpaid_leave_days',
        'regular_hours',
        'overtime_hours',
        'regular_pay',
        'paid_leave_pay',
        'overtime_pay',
        'gross_pay',
        'total_contributions',
        'total_deductions',
        'net_pay',
        'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'salary_snapshot' => 'array',
            'attendance_snapshot' => 'array',
            'leave_snapshot' => 'array',
            'overtime_snapshot' => 'array',
            'payable_days' => 'decimal:2',
            'days_present' => 'decimal:2',
            'paid_leave_days' => 'decimal:2',
            'unpaid_leave_days' => 'decimal:2',
            'regular_hours' => 'decimal:2',
            'overtime_hours' => 'decimal:2',
            'regular_pay' => 'decimal:2',
            'paid_leave_pay' => 'decimal:2',
            'overtime_pay' => 'decimal:2',
            'gross_pay' => 'decimal:2',
            'total_contributions' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'net_pay' => 'decimal:2',
            'finalized_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function payslip(): HasOne
    {
        return $this->hasOne(Payslip::class);
    }
}
