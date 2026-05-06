<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyTimeRecord extends Model
{
    use HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'employee_id',
        'date',
        'cutoff',
        'period_from',
        'period_to',
        'day_type',
        'attendance',
        'leave_type',
        'overtime_hours',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'period_from' => 'date',
            'period_to' => 'date',
            'overtime_hours' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
