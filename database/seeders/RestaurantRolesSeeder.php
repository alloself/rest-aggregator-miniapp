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

        // Создаем разрешения для ресторанов
        $permissions = [
            // Управление рестораном
            'restaurant.manage',
            'restaurant.edit',
            'restaurant.delete',
            
            // Управление меню
            'menu.view',
            'menu.create',
            'menu.edit',
            'menu.delete',
            
            // Управление заказами
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.delete',
            'orders.process',
            
            // Управление персоналом
            'staff.view',
            'staff.invite',
            'staff.edit',
            'staff.remove',
            
            // Управление столами
            'tables.view',
            'tables.create',
            'tables.edit',
            'tables.delete',
            
            // Кассовые операции
            'cash.view',
            'cash.process',
            'cash.reports',
            
            // Кухня
            'kitchen.view',
            'kitchen.process',
            
            // Обслуживание
            'service.view',
            'service.process',
            
            // Отчеты
            'reports.view',
            'reports.export',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Обновляем кеш после создания разрешений
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Создаем роли (они будут глобальными, но будут назначаться в контексте ресторанов)
        
        // Владелец ресторана - полный доступ
        $ownerRole = Role::firstOrCreate(['name' => 'restaurant_owner', 'guard_name' => 'web']);
        if ($ownerRole->permissions->isEmpty()) {
            $ownerRole->givePermissionTo(Permission::all());
        }

        // Менеджер ресторана - управление без удаления ресторана
        $managerRole = Role::firstOrCreate(['name' => 'restaurant_manager', 'guard_name' => 'web']);
        if ($managerRole->permissions->isEmpty()) {
            $managerRole->givePermissionTo([
                'restaurant.manage',
                'restaurant.edit',
                'menu.view', 'menu.create', 'menu.edit', 'menu.delete',
                'orders.view', 'orders.create', 'orders.edit', 'orders.delete', 'orders.process',
                'staff.view', 'staff.invite', 'staff.edit', 'staff.remove',
                'tables.view', 'tables.create', 'tables.edit', 'tables.delete',
                'cash.view', 'cash.process', 'cash.reports',
                'reports.view', 'reports.export',
            ]);
        }

        // Сотрудник (общий доступ)
        $staffRole = Role::firstOrCreate(['name' => 'restaurant_staff', 'guard_name' => 'web']);
        if ($staffRole->permissions->isEmpty()) {
            $staffRole->givePermissionTo([
                'menu.view',
                'orders.view', 'orders.create', 'orders.edit',
                'tables.view',
            ]);
        }

        // Официант
        $waiterRole = Role::firstOrCreate(['name' => 'restaurant_waiter', 'guard_name' => 'web']);
        if ($waiterRole->permissions->isEmpty()) {
            $waiterRole->givePermissionTo([
                'menu.view',
                'orders.view', 'orders.create', 'orders.edit', 'orders.process',
                'tables.view', 'tables.edit',
                'service.view', 'service.process',
            ]);
        }

        // Повар
        $cookRole = Role::firstOrCreate(['name' => 'restaurant_cook', 'guard_name' => 'web']);
        if ($cookRole->permissions->isEmpty()) {
            $cookRole->givePermissionTo([
                'menu.view',
                'orders.view', 'orders.process',
                'kitchen.view', 'kitchen.process',
            ]);
        }

        // Кассир
        $cashierRole = Role::firstOrCreate(['name' => 'restaurant_cashier', 'guard_name' => 'web']);
        if ($cashierRole->permissions->isEmpty()) {
            $cashierRole->givePermissionTo([
                'menu.view',
                'orders.view', 'orders.process',
                'cash.view', 'cash.process',
            ]);
        }
    }
}
