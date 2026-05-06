<?php

namespace App\Models\CRM;

use App\Models\Sales\Opportunity;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['invoice_id', 'customer_id', 'sale_id', 'amount', 'payment_method', 'reference_number', 'payment_date', 'received_by', 'notes'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'payment_date' => 'date'];
    }

    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function sale(): BelongsTo { return $this->belongsTo(Opportunity::class, 'sale_id'); }
    public function receiver(): BelongsTo { return $this->belongsTo(User::class, 'received_by'); }
}
