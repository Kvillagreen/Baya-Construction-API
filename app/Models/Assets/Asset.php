<?php

namespace App\Models\Assets;

use App\Models\HR\Employee;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasUlids, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'asset_category_id',
        'asset_code',
        'asset_name',
        'brand',
        'model',
        'serial_number',
        'asset_stocks',
        'model_serial_number',
        'asset_acquired_date',
        'purchase_cost',
        'current_value',
        'asset_condition',
        'location',
        'asset_price_encrypted',
        'assigned_employee_id',
        'released_to',
        'borrowed_by',
        'custodian',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'asset_acquired_date' => 'date',
            'asset_price_encrypted' => 'encrypted',
            'purchase_cost' => 'decimal:2',
            'current_value' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AssetLog::class);
    }
}
