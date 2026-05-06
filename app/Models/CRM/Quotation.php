<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sales\Opportunity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Quotation extends Model
{
    use HasUlids, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'quotation_code',
        'customer_id',
        'project_id',
        'sale_id',
        'title',
        'description',
        'scope_of_work',
        'amount',
        'content',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'status',
        'valid_until',
        'prepared_by',
        'approved_by',
        'quotation_price_encrypted',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'quotation_price_encrypted' => 'encrypted',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class, 'sale_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }
}
