<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $definitions = [
            [
                'code' => 'hr.overtime.update',
                'label' => 'Update overtime logs',
                'module' => 'hr',
                'service' => 'hr-payroll-service',
                'endpoint' => 'overtime-logs/{overtimeLog}',
                'method' => 'PATCH',
            ],
            [
                'code' => 'hr.overtime.delete',
                'label' => 'Delete overtime logs',
                'module' => 'hr',
                'service' => 'hr-payroll-service',
                'endpoint' => 'overtime-logs/{overtimeLog}',
                'method' => 'DELETE',
            ],
            [
                'code' => 'hr.leave.update',
                'label' => 'Update leave entries',
                'module' => 'hr',
                'service' => 'hr-payroll-service',
                'endpoint' => 'leave-entries/{leaveEntry}',
                'method' => 'PATCH',
            ],
            [
                'code' => 'hr.leave.delete',
                'label' => 'Delete leave entries',
                'module' => 'hr',
                'service' => 'hr-payroll-service',
                'endpoint' => 'leave-entries/{leaveEntry}',
                'method' => 'DELETE',
            ],
        ];

        $adminRoleId = DB::table('roles')->where('code', 'admin')->value('id');

        foreach ($definitions as $definition) {
            $permission = DB::table('permissions')->where('code', $definition['code'])->first();

            if (! $permission) {
                $permissionId = (string) Str::ulid();

                DB::table('permissions')->insert([
                    'id' => $permissionId,
                    'code' => $definition['code'],
                    'label' => $definition['label'],
                    'module' => $definition['module'],
                    'description' => $definition['label'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $permissionId = $permission->id;

                DB::table('permissions')
                    ->where('id', $permissionId)
                    ->update([
                        'label' => $definition['label'],
                        'module' => $definition['module'],
                        'description' => $definition['label'],
                        'updated_at' => now(),
                        'deleted_at' => null,
                    ]);
            }

            $endpointPermission = DB::table('endpoint_permissions')
                ->where('permission_id', $permissionId)
                ->where('endpoint', $definition['endpoint'])
                ->where('method', $definition['method'])
                ->first();

            if (! $endpointPermission) {
                DB::table('endpoint_permissions')->insert([
                    'id' => (string) Str::ulid(),
                    'permission_id' => $permissionId,
                    'service' => $definition['service'],
                    'endpoint' => $definition['endpoint'],
                    'method' => $definition['method'],
                    'description' => $definition['label'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('endpoint_permissions')
                    ->where('id', $endpointPermission->id)
                    ->update([
                        'service' => $definition['service'],
                        'description' => $definition['label'],
                        'updated_at' => now(),
                        'deleted_at' => null,
                    ]);
            }

            if ($adminRoleId && ! DB::table('role_permissions')
                ->where('role_id', $adminRoleId)
                ->where('permission_id', $permissionId)
                ->exists()) {
                DB::table('role_permissions')->insert([
                    'role_id' => $adminRoleId,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $codes = [
            'hr.overtime.update',
            'hr.overtime.delete',
            'hr.leave.update',
            'hr.leave.delete',
        ];

        $permissionIds = DB::table('permissions')
            ->whereIn('code', $codes)
            ->pluck('id');

        if ($permissionIds->isEmpty()) {
            return;
        }

        DB::table('role_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('endpoint_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }
};
