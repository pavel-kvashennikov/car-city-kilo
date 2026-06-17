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
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $tenantOwner = Role::firstOrCreate(['name' => 'tenant_owner']);
        $tenantOwner->givePermissionTo([
            'company.view', 'company.update',
            'vehicle.view', 'vehicle.create', 'vehicle.update', 'vehicle.delete', 'vehicle.publish',
            'product.view', 'product.create', 'product.update', 'product.delete', 'product.import',
            'appointment.view', 'appointment.create', 'appointment.update', 'appointment.manage',
            'service-item.manage',
            'order.view', 'order.manage',
            'staff.manage',
        ]);

        $salesManager = Role::firstOrCreate(['name' => 'sales_manager']);
        $salesManager->givePermissionTo([
            'vehicle.view', 'vehicle.create', 'vehicle.update', 'vehicle.publish',
        ]);

        $storekeeper = Role::firstOrCreate(['name' => 'storekeeper']);
        $storekeeper->givePermissionTo([
            'product.view', 'product.create', 'product.update', 'product.import',
            'order.view', 'order.manage',
        ]);

        $serviceAdvisor = Role::firstOrCreate(['name' => 'service_advisor']);
        $serviceAdvisor->givePermissionTo([
            'appointment.view', 'appointment.update', 'appointment.manage',
            'service-item.manage',
        ]);

        $buyer = Role::firstOrCreate(['name' => 'buyer']);
        $buyer->givePermissionTo([
            'order.view', 'appointment.create',
        ]);
    }
}
