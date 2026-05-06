<?php

namespace App\Models\CRM;

use App\Models\Sales\Opportunity;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['invoice_number', 'customer_id', 'project_id', 'sale_id', 'quotation_id', 'purchase_order_id', 'subtotal', 'discount', 'tax', 'total_amount', 'paid_amount', 'balance_due', 'status', 'due_date', 'issued_date', 'paid_at'];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_due' => 'decimal:2',
            'due_date' => 'date',
            'issued_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function sale(): BelongsTo { return $this->belongsTo(Opportunity::class, 'sale_id'); }
    public function quotation(): BelongsTo { return $this->belongsTo(Quotation::class); }
    public function purchaseOrder(): BelongsTo { return $this->belongsTo(PurchaseOrder::class); }
    public function items(): HasMany { return $this->hasMany(InvoiceItem::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
}
