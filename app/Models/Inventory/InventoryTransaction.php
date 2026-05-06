<?php

namespace App\Models\Inventory;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    use HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['item_id', 'transaction_type', 'quantity', 'previous_quantity', 'new_quantity', 'reference_type', 'reference_id', 'remarks', 'performed_by', 'transaction_date'];

    protected function casts(): array
    {
        return ['quantity' => 'decimal:2', 'previous_quantity' => 'decimal:2', 'new_quantity' => 'decimal:2', 'transaction_date' => 'datetime'];
    }

    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'item_id'); }
    public function performer(): BelongsTo { return $this->belongsTo(User::class, 'performed_by'); }
}
