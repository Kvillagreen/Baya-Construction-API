<?php

namespace App\Models\Assets;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetLog extends Model
{
    use HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['asset_id', 'action', 'previous_status', 'new_status', 'assigned_to', 'released_to', 'borrowed_by', 'remarks', 'performed_by'];

    public function asset(): BelongsTo { return $this->belongsTo(Asset::class); }
    public function performer(): BelongsTo { return $this->belongsTo(User::class, 'performed_by'); }
}
