<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\User;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем первого админа или создаем тестового
        $admin = User::first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Test Admin',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
            ]);
        }

        $restaurants = [
            [
                'name' => 'Ресторан "Золотая рыбка"',
                'description' => 'Уютный ресторан с авторской кухней и свежими морепродуктами',
                'address' => 'ул. Пушкина, 15',
                'phone' => '+7 (495) 123-45-67',
                'telegram_bot_token' => '1234567890:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA',
                'settings' => [
                    'working_hours' => '10:00-23:00',
                    'delivery' => true,
                    'min_order' => 1000
                ],
                'is_active' => true,
                'created_by_admin_id' => $admin->id,
            ],
            [
                'name' => 'Пиццерия "Мама Мия"',
                'description' => 'Настоящая итальянская пицца на дровах',
                'address' => 'пр. Ленина, 42',
                'phone' => '+7 (495) 987-65-43',
                'telegram_bot_token' => '0987654321:BBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBB',
                'settings' => [
                    'working_hours' => '11:00-01:00',
                    'delivery' => true,
                    'min_order' => 800
                ],
                'is_active' => true,
                'created_by_admin_id' => $admin->id,
            ],
            [
                'name' => 'Суши-бар "Сакура"',
                'description' => 'Японская кухня от шеф-повара из Токио',
                'address' => 'ул. Мира, 88',
                'phone' => '+7 (495) 555-77-99',
                'telegram_bot_token' => '1111111111:CCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCC',
                'settings' => [
                    'working_hours' => '12:00-24:00',
                    'delivery' => true,
                    'min_order' => 1500
                ],
                'is_active' => true,
                'created_by_admin_id' => $admin->id,
            ],
        ];

        foreach ($restaurants as $restaurant) {
            Restaurant::create($restaurant);
        }
    }
}
