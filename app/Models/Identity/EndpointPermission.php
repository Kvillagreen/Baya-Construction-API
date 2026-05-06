<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class EndpointPermission extends Model
{
    use HasUlids, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['permission_id', 'service', 'endpoint', 'method', 'description'];

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    public function userOverrides(): HasMany
    {
        return $this->hasMany(UserEndpointPermission::class);
    }
}
