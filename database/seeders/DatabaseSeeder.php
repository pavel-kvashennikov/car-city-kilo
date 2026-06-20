<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            CatalogSeeder::class,
            PlanSeeder::class,
        ]);

        // Глобальные роли (super_admin, buyer) привязаны к team_id = 0
        setPermissionsTeamId(0);

        $admin = User::firstOrCreate(
            ['email' => 'admin@automarket.ru'],
            ['name' => 'Admin', 'password' => bcrypt('password')]
        );
        if (! $admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }

        $buyer = User::firstOrCreate(
            ['email' => 'buyer@automarket.ru'],
            ['name' => 'Покупатель', 'password' => bcrypt('password')]
        );
        if (! $buyer->hasRole('buyer')) {
            $buyer->assignRole('buyer');
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->call(DemoSeeder::class);
    }
}
