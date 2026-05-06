<?php

namespace App\Models\Identity;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class UserEndpointPermission extends Model
{
    use HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['user_id', 'endpoint_permission_id', 'allowed'];

    protected function casts(): array
    {
        return [
            'allowed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function endpointPermission(): BelongsTo
    {
        return $this->belongsTo(EndpointPermission::class);
    }
}
