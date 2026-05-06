<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PayrollCutoffRefresh extends Model
{
    use HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'cutoff',
        'period_from',
        'period_to',
        'refreshed_at',
    ];

    protected function casts(): array
    {
        return [
            'period_from' => 'date',
            'period_to' => 'date',
            'refreshed_at' => 'datetime',
        ];
    }
}
