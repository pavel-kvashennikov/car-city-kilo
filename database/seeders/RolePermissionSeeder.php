<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $teamKey = config('permission.column_names.team_foreign_key', 'company_id');

        // Роли платформы (super_admin, buyer) — team_id = 0
        setPermissionsTeamId(0);

        // Create Permissions
        $permissions = [
            // Company
            'company.view', 'company.create', 'company.update', 'company.delete',
            'company.approve', 'company.suspend',
            // Vehicles
            'vehicle.view', 'vehicle.create', 'vehicle.update', 'vehicle.delete',
            'vehicle.publish', 'vehicle.moderate',
            // Products (parts)
            'product.view', 'product.create', 'product.update', 'product.delete',
            'product.import',
            // Service
            'appointment.view', 'appointment.create', 'appointment.update',
            'appointment.manage', 'service-item.manage',
            // Orders
            'order.view', 'order.manage',
            // Admin
            'admin.panel', 'billing.manage', 'market-map.manage',
            // Staff
            'staff.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and assign permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', $teamKey => 0]);
        $superAdmin->givePermissionTo(Permission::all());

        $tenantOwner = Role::firstOrCreate(['name' => 'tenant_owner', $teamKey => 0]);
        $tenantOwner->givePermissionTo([
            'company.view', 'company.update',
            'vehicle.view', 'vehicle.create', 'vehicle.update', 'vehicle.delete', 'vehicle.publish',
            'product.view', 'product.create', 'product.update', 'product.delete', 'product.import',
            'appointment.view', 'appointment.create', 'appointment.update', 'appointment.manage',
            'service-item.manage',
            'order.view', 'order.manage',
            'staff.manage',
        ]);

        $salesManager = Role::firstOrCreate(['name' => 'sales_manager', $teamKey => 0]);
        $salesManager->givePermissionTo([
            'vehicle.view', 'vehicle.create', 'vehicle.update', 'vehicle.publish',
        ]);

        $storekeeper = Role::firstOrCreate(['name' => 'storekeeper', $teamKey => 0]);
        $storekeeper->givePermissionTo([
            'product.view', 'product.create', 'product.update', 'product.import',
            'order.view', 'order.manage',
        ]);

        $serviceAdvisor = Role::firstOrCreate(['name' => 'service_advisor', $teamKey => 0]);
        $serviceAdvisor->givePermissionTo([
            'appointment.view', 'appointment.update', 'appointment.manage',
            'service-item.manage',
        ]);

        $buyer = Role::firstOrCreate(['name' => 'buyer', $teamKey => 0]);
        $buyer->givePermissionTo([
            'order.view', 'appointment.create',
        ]);
    }
}
