<?php

namespace App\Support;

use App\Models\Assets\Asset;
use App\Models\CRM\Customer;
use App\Models\CRM\Project;
use App\Models\CRM\ProjectTimeline;
use App\Models\CRM\Quotation;
use App\Models\HR\Employee;
use App\Models\HR\EmployeeLeaveBalance;
use App\Models\Identity\EndpointPermission;
use App\Models\Identity\Permission;
use App\Models\Identity\Role;
use App\Models\Identity\UserEndpointPermission;
use App\Models\Inventory\InventoryItem;
use App\Models\Sales\Opportunity;
use App\Models\User;

class FrontendApiPayload
{
    public static function user(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'user_type' => $user->user_type,
            'is_active' => (bool) $user->is_active,
            'last_login_at' => $user->last_login_at?->toISOString(),
            'roles' => $user->roles->map(fn (Role $role) => [
                'permissions' => $role->permissions->map(fn (Permission $permission) => [
                    'module' => $permission->module,
                    'endpoint_permissions' => $permission->endpointPermissions->map(fn (EndpointPermission $endpointPermission) => [
                        'endpoint' => $endpointPermission->endpoint,
                        'method' => $endpointPermission->method,
                    ])->values()->all(),
                ])->values()->all(),
            ])->values()->all(),
            'endpoint_overrides' => $user->endpointOverrides->map(fn (UserEndpointPermission $override) => [
                'allowed' => (bool) $override->allowed,
                'endpoint_permission' => $override->endpointPermission ? [
                    'endpoint' => $override->endpointPermission->endpoint,
                    'method' => $override->endpointPermission->method,
                ] : null,
            ])->values()->all(),
        ];
    }

    public static function employee(Employee $employee): array
    {
        return [
            'id' => $employee->id,
            'employee_id' => $employee->employee_id,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'position' => $employee->position,
            'date_hired' => $employee->date_hired?->toDateString(),
            'department' => $employee->department,
            'employment_status' => $employee->employment_status,
            'base_salary_encrypted' => $employee->base_salary_encrypted,
            'salary_type' => $employee->salary_type,
            'daily_rate' => $employee->daily_rate,
            'sss_contribution' => $employee->sss_contribution,
            'philhealth_contribution' => $employee->philhealth_contribution,
            'pagibig_contribution' => $employee->pagibig_contribution,
            'withholding_tax' => $employee->withholding_tax,
            'leave_balances' => $employee->leaveBalances->map(fn (EmployeeLeaveBalance $balance) => [
                'leave_type' => $balance->leave_type,
                'balance_days' => $balance->balance_days,
            ])->values()->all(),
        ];
    }

    public static function customer(Customer $customer): array
    {
        return [
            'id' => $customer->id,
            'company_name' => $customer->company_name,
            'company_address' => $customer->company_address,
            'region' => $customer->region,
            'location' => $customer->location,
            'status' => $customer->status,
            'contact_person' => $customer->contact_person,
            'contact_number' => $customer->contact_number,
            'projects' => $customer->projects->map(fn (Project $project) => [
                'id' => $project->id,
                'project_name' => $project->project_name,
                'description' => $project->description,
                'status' => $project->status,
                'invoice_number' => $project->invoice_number,
                'purchase_order_number' => $project->purchase_order_number,
                'project_costing_amount_encrypted' => $project->project_costing_amount_encrypted,
                'quotations' => $project->quotations->map(fn (Quotation $quotation) => [
                    'id' => $quotation->id,
                    'amount' => $quotation->amount,
                    'content' => $quotation->content,
                    'status' => $quotation->status,
                    'quotation_price_encrypted' => $quotation->quotation_price_encrypted,
                ])->values()->all(),
                'timelines' => $project->timelines->map(fn (ProjectTimeline $timeline) => [
                    'id' => $timeline->id,
                    'label' => $timeline->label,
                    'details' => $timeline->details,
                    'timeline_date' => $timeline->timeline_date?->toDateString(),
                ])->values()->all(),
            ])->values()->all(),
        ];
    }

    public static function asset(Asset $asset): array
    {
        return [
            'id' => $asset->id,
            'asset_name' => $asset->asset_name,
            'asset_stocks' => $asset->asset_stocks,
            'model_serial_number' => $asset->model_serial_number,
            'asset_acquired_date' => $asset->asset_acquired_date?->toDateString(),
            'asset_price_encrypted' => $asset->asset_price_encrypted,
            'status' => $asset->status,
            'assigned_employee_id' => $asset->assigned_employee_id,
            'category' => $asset->category ? [
                'name' => $asset->category->name,
            ] : null,
            'assigned_employee' => $asset->assignedEmployee ? [
                'id' => $asset->assignedEmployee->id,
                'first_name' => $asset->assignedEmployee->first_name,
                'last_name' => $asset->assignedEmployee->last_name,
            ] : null,
        ];
    }

    public static function inventoryItem(InventoryItem $item): array
    {
        return [
            'id' => $item->id,
            'item_name' => $item->item_name,
            'category' => $item->category,
            'available_stock' => $item->available_stock,
            'reserved_stock' => $item->reserved_stock,
            'reorder_point' => $item->reorder_point,
            'warehouse' => $item->warehouse,
        ];
    }

    public static function opportunity(Opportunity $opportunity): array
    {
        return [
            'id' => $opportunity->id,
            'account_name' => $opportunity->account_name,
            'project' => $opportunity->project,
            'stage' => $opportunity->stage,
            'amount' => $opportunity->amount,
            'owner_name' => $opportunity->owner_name,
            'close_date' => $opportunity->close_date?->toDateString(),
        ];
    }
}
