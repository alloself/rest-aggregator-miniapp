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
        $role = Role::create(['name' => 'root', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'root', 'guard_name' => 'web']);

        $role = Role::create(['name' => 'telegram_user', 'guard_name' => 'web']);
        $userPermission = Permission::create(['name' => 'telegram_user', 'guard_name' => 'web']);
    }
}
