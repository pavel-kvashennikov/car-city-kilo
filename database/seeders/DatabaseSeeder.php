<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            CatalogSeeder::class,
        ]);

        // Create SuperAdmin
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@automarket.ru',
        ]);
        $admin->assignRole('super_admin');

        // Create test buyer
        $buyer = User::factory()->create([
            'name' => 'Покупатель',
            'email' => 'buyer@automarket.ru',
        ]);
        $buyer->assignRole('buyer');
    }
}
