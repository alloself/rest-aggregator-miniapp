<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RestaurantRolesSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class CreateRestaurantUserCommand extends Command
{
    /**
     * Имя и сигнатура консольной команды.
     */
    protected $signature = 'create:restaurant-user
        {restaurant_name? : Название ресторана}
        {--email= : Email пользователя}
        {--password= : Пароль пользователя (если не указан — сгенерируем)}
        {--first_name= : Имя пользователя}
        {--last_name= : Фамилия пользователя}
        {--slug= : Явно указать slug ресторана}
        {--role=restaurant_owner : Роль для пользователя в ресторане (restaurant_owner|restaurant_manager|telegram_user)}
        {--no-seed : Не запускать сидер ролей/прав}';

    /**
     * Описание консольной команды.
     */
    protected $description = 'Создаёт ресторан, пользователя, связывает их и назначает роль в team-контексте (Spatie Permissions Teams)';

    /**
     * Выполнить консольную команду.
     */
    public function handle(): int
    {
        // 1) Получаем/спрашиваем входные данные
        $restaurant_name = (string) ($this->argument('restaurant_name') ?? '');
        if ($restaurant_name === '') {
            $restaurant_name = (string) $this->ask('Введите название ресторана');
            if ($restaurant_name === '') {
                $this->error('Название ресторана не может быть пустым');
                return self::FAILURE;
            }
        }

        $slug = (string) ($this->option('slug') ?? '');
        $email = (string) ($this->option('email') ?? '');
        if ($email === '') {
            $email = (string) $this->ask('Введите email пользователя');
        }

        $first_name = (string) ($this->option('first_name') ?? '');
        if ($first_name === '') {
            $first_name = (string) $this->anticipate('Введите имя пользователя (можно пропустить)', [], true) ?? '';
        }

        $last_name = (string) ($this->option('last_name') ?? '');
        if ($last_name === '') {
            $last_name = (string) $this->anticipate('Введите фамилию пользователя (можно пропустить)', [], true) ?? '';
        }

        $role = (string) ($this->option('role') ?? 'restaurant_owner');
        $allowed_roles = ['restaurant_owner', 'restaurant_manager', 'telegram_user'];
        if (!in_array($role, $allowed_roles, true)) {
            $this->error('Недопустимая роль: ' . $role . '. Разрешено: ' . implode(', ', $allowed_roles));
            return self::FAILURE;
        }

        // 2) Валидация email
        $validator = Validator::make(['email' => $email], [
            'email' => ['required', 'email'],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }
            return self::FAILURE;
        }

        // 3) По желанию — подготовим роли/права
        if (!$this->option('no-seed')) {
            // Сбросим кеш прежде чем обновлять/создавать роли
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            $this->info('Синхронизирую роли и разрешения (RestaurantRolesSeeder)...');
            $seedExit = Artisan::call('db:seed', [
                '--class' => RestaurantRolesSeeder::class,
                '--no-interaction' => true,
            ]);
            if ($seedExit !== 0) {
                $this->warn('Не удалось выполнить сидирование ролей. Продолжаю без него.');
            }
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }

        // 4) Создаём ресторан
        $restaurant = new Restaurant();
        $restaurant->name = $restaurant_name;
        if ($slug !== '') {
            $restaurant->slug = $slug;
        }
        $restaurant->save();

        // 5) Создаём (или находим) пользователя
        /** @var User|null $existing */
        $existing = User::where('email', $email)->first();

        $password = (string) ($this->option('password') ?? '');
        $generated_password = '';
        if ($password === '') {
            $generated_password = Str::password(16);
            $password = $generated_password;
        }

        if ($existing) {
            $user = $existing;
            $this->info('Пользователь с таким email уже существует — будет использован существующий аккаунт.');
        } else {
            $user = new User();
            $user->email = $email;
            $user->password = $password; // hash произойдёт через casts()
            if ($first_name !== '') {
                $user->first_name = $first_name;
            }
            if ($last_name !== '') {
                $user->last_name = $last_name;
            }
            $user->email_verified_at = now();
            $user->save();
        }

        // 6) Связываем пользователя с рестораном и назначаем роль в team-контексте
        $restaurant->addUser($user, $role);

        // 7) Выводим результат
        $this->line('');
        $this->info('✅ Готово! Создана/настроена связка ресторан ↔ пользователь.');
        $this->line('');
        $this->line('Ресторан:');
        $this->line('  ID:   ' . $restaurant->getKey());
        $this->line('  Name: ' . (string) $restaurant->name);
        $this->line('  Slug: ' . (string) $restaurant->slug);
        $this->line('');
        $this->line('Пользователь:');
        $this->line('  ID:    ' . $user->getKey());
        $this->line('  Email: ' . (string) $user->email);
        if ($generated_password !== '') {
            $this->line('  Пароль (сгенерирован): ' . $generated_password);
        }
        $this->line('');
        $this->line('Назначенная роль: ' . $role . ' (team_id = ' . $restaurant->getKey() . ')');

        return self::SUCCESS;
    }
}


