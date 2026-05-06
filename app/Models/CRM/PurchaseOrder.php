<?php

namespace App\Models\CRM;

use App\Models\Sales\Opportunity;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrder extends Model
{
    use HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['po_number', 'customer_id', 'project_id', 'sale_id', 'quotation_id', 'title', 'description', 'amount', 'status', 'po_date', 'received_date', 'attachment_url', 'created_by'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'po_date' => 'date', 'received_date' => 'date'];
    }

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function sale(): BelongsTo { return $this->belongsTo(Opportunity::class, 'sale_id'); }
    public function quotation(): BelongsTo { return $this->belongsTo(Quotation::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
