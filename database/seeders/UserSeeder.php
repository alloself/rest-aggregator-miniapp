<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $root = User::create([
            'first_name' => 'root',
            'last_name' => 'root',
            'email_verified_at' => null,
            'email' => 'root@example.com',
            'password' => Hash::make('password'),
        ]);

        // Назначаем роль root в контексте команды (team) чтобы удовлетворить NOT NULL team_id
        $currentTeamId = getPermissionsTeamId();
        setPermissionsTeamId($root->id);
        $root->assignRole('root');
        setPermissionsTeamId($currentTeamId);

        // Менеджер ресторана (будет привязан к ресторану в RestaurantSeeder)
        User::create([
            'first_name' => 'manager',
            'last_name' => 'manager',
            'email_verified_at' => null,
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
        ]);

        // Телеграм-читатель (будет привязан к ресторану в RestaurantSeeder)
        User::create([
            'first_name' => 'reader',
            'last_name' => 'reader',
            'email_verified_at' => null,
            'email' => 'reader@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
