<?php

namespace Database\Seeders;

use App\Models\Assets\Asset;
use App\Models\Assets\AssetCategory;
use App\Models\CRM\Customer;
use App\Models\CRM\Project;
use App\Models\CRM\ProjectTimeline;
use App\Models\CRM\Quotation;
use App\Models\HR\Employee;
use App\Models\HR\EmployeeLeaveBalance;
use App\Models\Identity\EndpointPermission;
use App\Models\Identity\Permission;
use App\Models\Identity\Role;
use App\Models\Inventory\InventoryItem;
use App\Models\Sales\Opportunity;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::query()->updateOrCreate(
            ['code' => 'admin'],
            [
                'name' => 'Administrator',
                'description' => 'Full operational control across the Baya platform.',
            ]
        );

        $operationsRole = Role::query()->updateOrCreate(
            ['code' => 'operations_user'],
            [
                'name' => 'Operations User',
                'description' => 'Limited operational access across approved modules.',
            ]
        );

        $permissions = collect([
            ['code' => 'dashboard.view', 'label' => 'View dashboard', 'module' => 'dashboard', 'service' => 'dashboard-service', 'endpoint' => 'dashboard/summary', 'method' => 'GET'],
            ['code' => 'users.manage', 'label' => 'Manage users', 'module' => 'users', 'service' => 'identity-access-service', 'endpoint' => 'users', 'method' => 'GET'],
            ['code' => 'users.create', 'label' => 'Create users', 'module' => 'users', 'service' => 'identity-access-service', 'endpoint' => 'users', 'method' => 'POST'],
            ['code' => 'users.update', 'label' => 'Update users', 'module' => 'users', 'service' => 'identity-access-service', 'endpoint' => 'users/{user}', 'method' => 'PATCH'],
            ['code' => 'users.delete', 'label' => 'Delete users', 'module' => 'users', 'service' => 'identity-access-service', 'endpoint' => 'users/{user}', 'method' => 'DELETE'],
            ['code' => 'crm.view', 'label' => 'View customers', 'module' => 'crm', 'service' => 'crm-service', 'endpoint' => 'customers', 'method' => 'GET'],
            ['code' => 'crm.view.customer', 'label' => 'View customer detail', 'module' => 'crm', 'service' => 'crm-service', 'endpoint' => 'customers/{customer}', 'method' => 'GET'],
            ['code' => 'crm.create', 'label' => 'Create customers', 'module' => 'crm', 'service' => 'crm-service', 'endpoint' => 'customers', 'method' => 'POST'],
            ['code' => 'crm.update', 'label' => 'Update customers', 'module' => 'crm', 'service' => 'crm-service', 'endpoint' => 'customers/{customer}', 'method' => 'PATCH'],
            ['code' => 'crm.delete', 'label' => 'Delete customers', 'module' => 'crm', 'service' => 'crm-service', 'endpoint' => 'customers/{customer}', 'method' => 'DELETE'],
            ['code' => 'hr.view', 'label' => 'View employees', 'module' => 'hr', 'service' => 'hr-payroll-service', 'endpoint' => 'employees', 'method' => 'GET'],
            ['code' => 'hr.create', 'label' => 'Create employees', 'module' => 'hr', 'service' => 'hr-payroll-service', 'endpoint' => 'employees', 'method' => 'POST'],
            ['code' => 'hr.update', 'label' => 'Update employees', 'module' => 'hr', 'service' => 'hr-payroll-service', 'endpoint' => 'employees/{employee}', 'method' => 'PATCH'],
            ['code' => 'hr.delete', 'label' => 'Delete employees', 'module' => 'hr', 'service' => 'hr-payroll-service', 'endpoint' => 'employees/{employee}', 'method' => 'DELETE'],
            ['code' => 'hr.overtime.update', 'label' => 'Update overtime logs', 'module' => 'hr', 'service' => 'hr-payroll-service', 'endpoint' => 'overtime-logs/{overtimeLog}', 'method' => 'PATCH'],
            ['code' => 'hr.overtime.delete', 'label' => 'Delete overtime logs', 'module' => 'hr', 'service' => 'hr-payroll-service', 'endpoint' => 'overtime-logs/{overtimeLog}', 'method' => 'DELETE'],
            ['code' => 'hr.leave.update', 'label' => 'Update leave entries', 'module' => 'hr', 'service' => 'hr-payroll-service', 'endpoint' => 'leave-entries/{leaveEntry}', 'method' => 'PATCH'],
            ['code' => 'hr.leave.delete', 'label' => 'Delete leave entries', 'module' => 'hr', 'service' => 'hr-payroll-service', 'endpoint' => 'leave-entries/{leaveEntry}', 'method' => 'DELETE'],
            ['code' => 'payroll.view', 'label' => 'View payroll', 'module' => 'hr', 'service' => 'hr-payroll-service', 'endpoint' => 'payroll/runs', 'method' => 'GET'],
            ['code' => 'assets.view', 'label' => 'View assets', 'module' => 'assets', 'service' => 'asset-inventory-service', 'endpoint' => 'assets', 'method' => 'GET'],
            ['code' => 'assets.create', 'label' => 'Create assets', 'module' => 'assets', 'service' => 'asset-inventory-service', 'endpoint' => 'assets', 'method' => 'POST'],
            ['code' => 'assets.update', 'label' => 'Update assets', 'module' => 'assets', 'service' => 'asset-inventory-service', 'endpoint' => 'assets/{asset}', 'method' => 'PATCH'],
            ['code' => 'assets.delete', 'label' => 'Delete assets', 'module' => 'assets', 'service' => 'asset-inventory-service', 'endpoint' => 'assets/{asset}', 'method' => 'DELETE'],
            ['code' => 'inventory.view', 'label' => 'View inventory', 'module' => 'inventory', 'service' => 'asset-inventory-service', 'endpoint' => 'inventory/items', 'method' => 'GET'],
            ['code' => 'inventory.create', 'label' => 'Create inventory items', 'module' => 'inventory', 'service' => 'asset-inventory-service', 'endpoint' => 'inventory/items', 'method' => 'POST'],
            ['code' => 'inventory.update', 'label' => 'Update inventory items', 'module' => 'inventory', 'service' => 'asset-inventory-service', 'endpoint' => 'inventory/items/{inventoryItem}', 'method' => 'PATCH'],
            ['code' => 'inventory.delete', 'label' => 'Delete inventory items', 'module' => 'inventory', 'service' => 'asset-inventory-service', 'endpoint' => 'inventory/items/{inventoryItem}', 'method' => 'DELETE'],
            ['code' => 'sales.view', 'label' => 'View sales opportunities', 'module' => 'sales', 'service' => 'sales-service', 'endpoint' => 'sales/opportunities', 'method' => 'GET'],
            ['code' => 'sales.create', 'label' => 'Create sales opportunities', 'module' => 'sales', 'service' => 'sales-service', 'endpoint' => 'sales/opportunities', 'method' => 'POST'],
            ['code' => 'sales.update', 'label' => 'Update sales opportunities', 'module' => 'sales', 'service' => 'sales-service', 'endpoint' => 'sales/opportunities/{opportunity}', 'method' => 'PATCH'],
            ['code' => 'sales.delete', 'label' => 'Delete sales opportunities', 'module' => 'sales', 'service' => 'sales-service', 'endpoint' => 'sales/opportunities/{opportunity}', 'method' => 'DELETE'],
            ['code' => 'documents.presign', 'label' => 'Presign document uploads', 'module' => 'documents', 'service' => 'document-service', 'endpoint' => 'documents/presign', 'method' => 'POST'],
        ])->map(function (array $definition): Permission {
            $permission = Permission::query()->updateOrCreate(
                ['code' => $definition['code']],
                [
                    'label' => $definition['label'],
                    'module' => $definition['module'],
                    'description' => $definition['label'],
                ]
            );

            EndpointPermission::query()->updateOrCreate(
                [
                    'permission_id' => $permission->id,
                    'endpoint' => $definition['endpoint'],
                    'method' => $definition['method'],
                ],
                [
                    'service' => $definition['service'],
                    'description' => $definition['label'],
                ]
            );

            return $permission;
        });

        $adminRole->permissions()->sync($permissions->pluck('id')->all());
        $operationsRole->permissions()->sync(
            $permissions
                ->whereIn('code', ['dashboard.view', 'crm.view', 'crm.view.customer', 'crm.update', 'assets.view', 'assets.update', 'inventory.view', 'sales.view', 'sales.update', 'hr.view'])
                ->pluck('id')
                ->all()
        );

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@bayaconstruction.com'],
            [
                'name' => 'Mara Santos',
                'password' => 'admin123',
                'user_type' => 'Admin',
                'is_active' => true,
                'last_login_at' => now(),
            ]
        );
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $user = User::query()->updateOrCreate(
            ['email' => 'user@bayaconstruction.com'],
            [
                'name' => 'Ian dela Cruz',
                'password' => 'admin123',
                'user_type' => 'User',
                'is_active' => true,
            ]
        );
        $user->roles()->syncWithoutDetaching([$operationsRole->id]);

        $employee = Employee::query()->updateOrCreate(
            ['employee_id' => '2024-001'],
            [
                'first_name' => 'Ana',
                'last_name' => 'Villanueva',
                'position' => 'HR Supervisor',
                'date_hired' => '2024-02-12',
                'department' => 'Human Resources',
                'employment_status' => 'Regular',
                'base_salary_encrypted' => '42000',
            ]
        );

        foreach (['Vacation' => 10, 'Sick' => 8, 'Emergency' => 3, 'Other' => 2] as $type => $balance) {
            EmployeeLeaveBalance::query()->updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'leave_type' => $type,
                ],
                ['balance_days' => $balance]
            );
        }

        $customer = Customer::query()->updateOrCreate(
            ['company_name' => 'North Ridge Properties'],
            [
                'company_address' => 'Makati City, Metro Manila',
                'region' => 'NCR',
                'location' => 'Makati',
                'status' => 'approved',
                'contact_person' => 'Leandro Uy',
                'contact_number' => '+63 917 555 0191',
            ]
        );

        $project = Project::query()->updateOrCreate(
            [
                'customer_id' => $customer->id,
                'project_name' => 'Commercial Fit-Out Tower B',
            ],
            [
                'description' => 'Full structural retrofit and interior fit-out package.',
                'status' => 'approved',
                'invoice_number' => 'INV-2026-0018',
                'purchase_order_number' => 'PO-88213',
                'project_costing_amount_encrypted' => '18500000',
            ]
        );

        Quotation::query()->updateOrCreate(
            ['project_id' => $project->id],
            [
                'amount' => 17450000,
                'content' => 'Structural scope and MEP coordination',
                'status' => 'approved',
                'quotation_price_encrypted' => '17450000',
            ]
        );

        ProjectTimeline::query()->updateOrCreate(
            [
                'project_id' => $project->id,
                'label' => 'Mobilization',
            ],
            [
                'details' => 'Field team assigned and ready for rollout.',
                'timeline_date' => '2026-03-01',
            ]
        );

        $category = AssetCategory::query()->updateOrCreate(
            ['name' => 'Survey Equipment'],
            ['description' => 'Precision construction measurement equipment.']
        );

        Asset::query()->updateOrCreate(
            ['model_serial_number' => 'TS-9001-A'],
            [
                'asset_category_id' => $category->id,
                'asset_name' => 'Total Station',
                'asset_stocks' => 4,
                'asset_acquired_date' => '2025-10-14',
                'asset_price_encrypted' => '780000',
                'assigned_employee_id' => $employee->id,
                'status' => 'Assigned',
            ]
        );

        InventoryItem::query()->updateOrCreate(
            ['item_name' => 'Portland Cement'],
            [
                'category' => 'Materials',
                'available_stock' => 510,
                'reserved_stock' => 120,
                'reorder_point' => 200,
                'warehouse' => 'Valenzuela Yard',
            ]
        );

        Opportunity::query()->updateOrCreate(
            [
                'account_name' => 'North Ridge Properties',
                'project' => 'Tower B Fit-Out',
            ],
            [
                'stage' => 'Awarded',
                'amount' => 18500000,
                'owner_name' => 'Jerome Tan',
                'close_date' => '2026-04-17',
            ]
        );
    }
}
