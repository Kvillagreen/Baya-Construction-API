<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class FailedLoginAttempt extends Model
{
    use HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'attempt_count',
        'geo_hint',
        'last_attempt_at',
    ];

    protected function casts(): array
    {
        return [
            'last_attempt_at' => 'datetime',
        ];
    }
}
