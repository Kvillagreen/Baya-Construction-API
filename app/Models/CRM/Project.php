<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sales\Opportunity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Project extends Model
{
    use HasUlids, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'customer_id',
        'sale_id',
        'quotation_id',
        'project_code',
        'project_name',
        'title',
        'description',
        'scope_of_work',
        'estimated_price',
        'approved_price',
        'status',
        'start_date',
        'target_completion_date',
        'completed_at',
        'created_by',
        'assigned_to',
        'invoice_number',
        'purchase_order_number',
        'project_costing_amount_encrypted',
    ];

    protected function casts(): array
    {
        return [
            'project_costing_amount_encrypted' => 'encrypted',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class, 'sale_id');
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function timelines(): HasMany
    {
        return $this->hasMany(ProjectTimeline::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ProjectLog::class);
    }
}
