<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;


class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Базовая роль root (для bootstrap и локального доступа)
        Role::firstOrCreate(['name' => 'root', 'guard_name' => 'web']);

        // Минимальный набор permissions: должен совпадать с middleware AccountPermissionMiddleware
        // restaurant.* и menu.*
        $permissions = [
            'restaurant.manage', 'restaurant.edit', 'restaurant.delete',
            'menu.view', 'menu.create', 'menu.edit', 'menu.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
