<?php

namespace App\Models\Assets;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetAssignment extends Model
{
    use HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['asset_id', 'assigned_to', 'released_by', 'assigned_date', 'expected_return_date', 'returned_date', 'status', 'notes'];

    protected function casts(): array
    {
        return ['assigned_date' => 'date', 'expected_return_date' => 'date', 'returned_date' => 'date'];
    }

    public function asset(): BelongsTo { return $this->belongsTo(Asset::class); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function releaser(): BelongsTo { return $this->belongsTo(User::class, 'released_by'); }
}
