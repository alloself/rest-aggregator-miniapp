<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем роли если их нет
        $adminRole = Role::where('name', 'admin')->first();
        if (!$adminRole) {
            $adminRole = new Role();
            $adminRole->id = (string) Str::uuid();
            $adminRole->name = 'admin';
            $adminRole->guard_name = 'web';
            $adminRole->save();
        }

        $restaurantOwnerRole = Role::where('name', 'restaurant_owner')->first();
        if (!$restaurantOwnerRole) {
            $restaurantOwnerRole = new Role();
            $restaurantOwnerRole->id = (string) Str::uuid();
            $restaurantOwnerRole->name = 'restaurant_owner';
            $restaurantOwnerRole->guard_name = 'web';
            $restaurantOwnerRole->save();
        }

        // Создаем базовые разрешения
        $permissions = [
            'restaurants.view',
            'restaurants.create',
            'restaurants.update',
            'restaurants.delete',
            'menus.view',
            'menus.create',
            'menus.update',
            'menus.delete',
            'dishes.view',
            'dishes.create',
            'dishes.update',
            'dishes.delete',
            'events.view',
            'events.create',
            'events.update',
            'events.delete',
            'news.view',
            'news.create',
            'news.update',
            'news.delete',
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',
        ];

        foreach ($permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if (!$permission) {
                $permission = new Permission();
                $permission->id = (string) Str::uuid();
                $permission->name = $permissionName;
                $permission->guard_name = 'web';
                $permission->save();
            }
        }

        // Даем админу все права
        $adminRole->givePermissionTo(Permission::all());

        // Даем владельцу ресторана права на свой контент
        $restaurantOwnerRole->givePermissionTo([
            'menus.view', 'menus.create', 'menus.update', 'menus.delete',
            'dishes.view', 'dishes.create', 'dishes.update', 'dishes.delete',
            'events.view', 'events.create', 'events.update', 'events.delete',
            'news.view', 'news.create', 'news.update', 'news.delete',
            'restaurants.view', 'restaurants.update',
            'categories.view',
        ]);

        // Создаем тестового админа
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Platform Administrator',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        // Получаем первый ресторан для владельца
        $restaurant = Restaurant::first();
        
        if ($restaurant) {
            // Создаем владельца ресторана
            $restaurantOwner = User::firstOrCreate(
                ['email' => 'owner@pizza-palace.com'],
                [
                    'name' => 'Pizza Palace Owner',
                    'password' => Hash::make('password'),
                    'restaurant_id' => $restaurant->id,
                ]
            );
            $restaurantOwner->assignRole('restaurant_owner');

            $this->command->info('✅ Создан владелец ресторана: owner@pizza-palace.com / password');
        }

        // Создаем второго владельца для другого ресторана
        $secondRestaurant = Restaurant::skip(1)->first();
        
        if ($secondRestaurant) {
            $secondOwner = User::firstOrCreate(
                ['email' => 'owner@sushi-zen.com'],
                [
                    'name' => 'Sushi Zen Owner',
                    'password' => Hash::make('password'),
                    'restaurant_id' => $secondRestaurant->id,
                ]
            );
            $secondOwner->assignRole('restaurant_owner');

            $this->command->info('✅ Создан владелец ресторана: owner@sushi-zen.com / password');
        }

        $this->command->info('✅ Создан администратор: admin@example.com / password');
        $this->command->info('🔑 Роли и разрешения настроены');
    }
}
