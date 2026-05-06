<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;
use App\Models\CRM\Invoice;
use App\Models\CRM\Payment;
use App\Models\CRM\PurchaseOrder;
use App\Models\Sales\Opportunity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Customer extends Model
{
    use HasUlids, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'customer_code',
        'customer_type',
        'company_name',
        'company_address',
        'region',
        'location',
        'industry',
        'source',
        'status',
        'notes',
        'contact_person',
        'email',
        'phone',
        'contact_number',
        'created_by',
        'assigned_to',
        'is_active',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(CustomerContact::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CustomerActivity::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(CustomerCommunicationLog::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Opportunity::class, 'customer_id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }
}
