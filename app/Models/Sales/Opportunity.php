<?php

namespace App\Models\Sales;

use App\Models\CRM\Customer;
use App\Models\CRM\Invoice;
use App\Models\CRM\Project;
use App\Models\CRM\PurchaseOrder;
use App\Models\CRM\Quotation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Opportunity extends Model
{
    use HasUlids, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'customer_id',
        'project_id',
        'quotation_id',
        'sales_code',
        'title',
        'description',
        'account_name',
        'project',
        'stage',
        'status',
        'amount',
        'owner_name',
        'probability',
        'close_date',
        'expected_close_date',
        'closed_at',
        'lost_reason',
        'assigned_to',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'close_date' => 'date',
            'expected_close_date' => 'date',
            'closed_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function projectRecord(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'sale_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'sale_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(SalesLog::class, 'sale_id');
    }
}
