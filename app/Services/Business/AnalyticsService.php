<?php

namespace App\Services\Business;

use App\Models\Assets\Asset;
use App\Models\CRM\Customer;
use App\Models\CRM\Invoice;
use App\Models\CRM\Quotation;
use App\Models\Inventory\InventoryItem;
use App\Models\Inventory\InventoryTransaction;
use App\Models\Sales\Opportunity;

class AnalyticsService
{
    public function customerSummary(): array
    {
        return [
            'total_customers' => Customer::query()->count(),
            'active_customers' => Customer::query()->where('is_active', true)->count(),
            'inactive_customers' => Customer::query()->where('is_active', false)->count(),
            'quotation_conversion_rate' => $this->ratio(
                Quotation::query()->whereIn('status', ['Accepted', 'Converted'])->count(),
                Quotation::query()->count()
            ),
            'sales_pipeline_value' => (float) Opportunity::query()->sum('amount'),
            'unpaid_invoice_amount' => (float) Invoice::query()->sum('balance_due'),
        ];
    }

    public function salesSummary(): array
    {
        return [
            'total_sales_value' => (float) Opportunity::query()->sum('amount'),
            'won_sales' => Opportunity::query()->where('stage', 'Won')->count(),
            'lost_sales' => Opportunity::query()->where('stage', 'Lost')->count(),
            'open_quotations' => Quotation::query()->whereIn('status', ['Draft', 'Sent'])->count(),
            'unpaid_invoices' => Invoice::query()->where('balance_due', '>', 0)->count(),
        ];
    }

    public function inventorySummary(): array
    {
        return [
            'total_items' => InventoryItem::query()->count(),
            'low_stock_items' => InventoryItem::query()->whereColumn('available_stock', '<=', 'reorder_point')->count(),
            'out_of_stock_items' => InventoryItem::query()->where('available_stock', '<=', 0)->count(),
            'stock_value' => (float) InventoryItem::query()->selectRaw('SUM(quantity_on_hand * cost_price) as total')->value('total'),
            'recent_movements' => InventoryTransaction::query()->latest('transaction_date')->limit(10)->get(),
        ];
    }

    public function assetSummary(): array
    {
        return [
            'total_assets' => Asset::query()->count(),
            'available_assets' => Asset::query()->where('status', 'Available')->count(),
            'assigned_assets' => Asset::query()->where('status', 'Assigned')->count(),
            'borrowed_assets' => Asset::query()->where('status', 'Borrowed')->count(),
            'damaged_assets' => Asset::query()->where('status', 'Damaged')->count(),
            'lost_assets' => Asset::query()->where('status', 'Lost')->count(),
        ];
    }

    private function ratio(int $numerator, int $denominator): float
    {
        return $denominator > 0 ? round(($numerator / $denominator) * 100, 2) : 0;
    }
}
