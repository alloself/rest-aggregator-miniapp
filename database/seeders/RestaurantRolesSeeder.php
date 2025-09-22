<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RestaurantRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Сброс кешированных ролей и разрешений
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Минимальный набор прав (синхронизирован с PermissionSeeder и middleware)
        $permissions = [
            'restaurant.manage', 'restaurant.edit', 'restaurant.delete',
            'menu.view', 'menu.create', 'menu.edit', 'menu.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Обновляем кеш после создания разрешений
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Владелец — полный доступ
        $ownerRole = Role::firstOrCreate(['name' => 'restaurant_owner', 'guard_name' => 'web']);
        $ownerRole->syncPermissions(Permission::all());

        // Менеджер — управление без удаления ресторана
        $managerRole = Role::firstOrCreate(['name' => 'restaurant_manager', 'guard_name' => 'web']);
        $managerRole->syncPermissions([
            'restaurant.manage', 'restaurant.edit',
            'menu.view', 'menu.create', 'menu.edit', 'menu.delete',
        ]);

        // Телеграм-читатель — только просмотр меню
        $telegramUserRole = Role::firstOrCreate(['name' => 'telegram_user', 'guard_name' => 'web']);
        $telegramUserRole->syncPermissions(['menu.view']);
    }
}
