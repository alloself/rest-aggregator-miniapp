<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем root пользователя
        $rootUser = User::where('email', 'root@example.com')->first();
        $managerUser = User::where('email', 'manager@example.com')->first();
        $readerUser = User::where('email', 'reader@example.com')->first();

        if (!$rootUser || !$managerUser || !$readerUser) {
            $this->command->error('Не найдены базовые пользователи (root/manager/reader). Убедитесь, что UserSeeder выполнен.');
            return;
        }

        // Создаем ресторан gretto
        $grettoRestaurant = Restaurant::create([
            'name' => 'Gretto',
            'slug' => 'gretto',
            'subtitle' => 'Средиземноморские завтраки и обеды. Греческая кухня.',
            'description' => 'Ресторан Gretto предлагает изысканную кухню в уютной домашней атмосфере. Мы готовим из свежих продуктов и создаем незабываемые вкусовые впечатления.',
            'address' => 'Наб. реки Фонтанки, 1',
            'phone' => '+7 (812) 123-45-67',
            'telegram_bot_token' => null, // Здесь можно указать токен бота для тестирования
            'average_receipt' => '1500-2500 руб.',
            'working_hours' => [
                'friday' => [
                    'endTime' => '22:00',
                    'startTime' => '09:00'
                ],
                'monday' => [
                    'endTime' => '22:00',
                    'startTime' => '09:00'
                ],
                'sunday' => [
                    'endTime' => '22:00',
                    'startTime' => '09:00'
                ],
                'tuesday' => [
                    'endTime' => '22:00',
                    'startTime' => '09:00'
                ],
                'saturday' => [
                    'endTime' => '22:00',
                    'startTime' => '09:00'
                ],
                'thursday' => [
                    'endTime' => '22:00',
                    'startTime' => '09:00'
                ],
                'wednesday' => [
                    'endTime' => '22:00',
                    'startTime' => '09:00'
                ]
            ],
            'user_id' => $rootUser->id,
        ]);

        // Добавляем root пользователя как владельца ресторана
        // Гарантируем наличие ролей перед назначением
        Role::firstOrCreate(['name' => 'restaurant_owner', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'restaurant_manager', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'telegram_user', 'guard_name' => 'web']);

        $grettoRestaurant->addUser($rootUser, 'restaurant_owner');

        // Привязываем менеджера и назначаем роль restaurant_manager
        $grettoRestaurant->addUser($managerUser, 'restaurant_manager');

        // Привязываем читателя и назначаем роль telegram_user (читатель)
        $grettoRestaurant->addUser($readerUser, 'telegram_user');

        $this->command->info('Ресторан Gretto успешно создан и связан с root пользователем.');
    }
}
