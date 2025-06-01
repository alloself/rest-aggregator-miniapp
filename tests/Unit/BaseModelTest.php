<?php

namespace Tests\Unit;

use App\Models\TelegramUser;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BaseModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_base_model_uses_uuid_trait(): void
    {
        $model = new TelegramUser();
        
        $this->assertContains(HasUuids::class, class_uses_recursive($model));
        $this->assertFalse($model->getIncrementing());
        $this->assertEquals('string', $model->getKeyType());
    }

    public function test_telegram_user_model_creates_uuid(): void
    {
        $user = TelegramUser::create([
            'telegram_id' => 123456789,
            'username' => 'testuser',
            'first_name' => 'Test',
            'settings' => ['language' => 'en'],
        ]);

        $this->assertIsString($user->id);
        $this->assertTrue($this->isValidUuid($user->id));
        $this->assertEquals(123456789, $user->telegram_id);
        $this->assertEquals('testuser', $user->username);
    }

    public function test_find_by_telegram_id(): void
    {
        $originalUser = TelegramUser::create([
            'telegram_id' => 987654321,
            'username' => 'findme',
            'first_name' => 'Find',
        ]);

        $foundUser = TelegramUser::findByTelegramId(987654321);

        $this->assertNotNull($foundUser);
        $this->assertEquals($originalUser->id, $foundUser->id);
        $this->assertEquals('findme', $foundUser->username);
    }

    public function test_create_from_telegram(): void
    {
        $telegramData = [
            'id' => 111222333,
            'username' => 'telegramuser',
            'first_name' => 'Telegram',
            'last_name' => 'User',
            'language_code' => 'ru',
            'is_bot' => false,
        ];

        $user = TelegramUser::createFromTelegram($telegramData);

        $this->assertInstanceOf(TelegramUser::class, $user);
        $this->assertEquals(111222333, $user->telegram_id);
        $this->assertEquals('telegramuser', $user->username);
        $this->assertEquals('Telegram', $user->first_name);
        $this->assertEquals('User', $user->last_name);
        $this->assertEquals('ru', $user->language_code);
        $this->assertFalse($user->is_bot);
    }

    public function test_casts_work_correctly(): void
    {
        $user = TelegramUser::create([
            'telegram_id' => 444555666,
            'settings' => ['theme' => 'dark', 'notifications' => true],
            'is_bot' => true,
        ]);

        $this->assertIsArray($user->settings);
        $this->assertEquals(['theme' => 'dark', 'notifications' => true], $user->settings);
        $this->assertTrue($user->is_bot);
        $this->assertInstanceOf(\DateTime::class, $user->created_at);
    }

    public function test_telegram_user_soft_deletes(): void
    {
        $user = TelegramUser::create([
            'telegram_id' => 777888999,
            'username' => 'softdelete_user',
            'first_name' => 'Soft',
        ]);

        $userId = $user->id;

        // Soft delete пользователя
        $user->delete();

        // Проверяем что пользователь не найден в обычном поиске
        $this->assertNull(TelegramUser::find($userId));

        // Но найден в withTrashed()
        $trashedUser = TelegramUser::withTrashed()->find($userId);
        $this->assertNotNull($trashedUser);
        $this->assertNotNull($trashedUser->deleted_at);
        $this->assertTrue($trashedUser->trashed());

        // Можем восстановить
        $trashedUser->restore();
        $this->assertNotNull(TelegramUser::find($userId));
        $this->assertNull(TelegramUser::find($userId)->deleted_at);
    }

    public function test_find_by_telegram_id_with_trashed(): void
    {
        $user = TelegramUser::create([
            'telegram_id' => 123123123,
            'username' => 'trashed_user',
            'first_name' => 'Trashed',
        ]);

        $telegramId = $user->telegram_id;

        // Обычный поиск работает
        $this->assertNotNull(TelegramUser::findByTelegramId($telegramId));

        // Soft delete пользователя
        $user->delete();

        // Обычный поиск не находит
        $this->assertNull(TelegramUser::findByTelegramId($telegramId));

        // Поиск с трэшем находит
        $trashedUser = TelegramUser::findByTelegramIdWithTrashed($telegramId);
        $this->assertNotNull($trashedUser);
        $this->assertTrue($trashedUser->trashed());
        $this->assertEquals('trashed_user', $trashedUser->username);
    }

    /**
     * Check if string is valid UUID (v4 or v7)
     */
    private function isValidUuid(string $uuid): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[47][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid) === 1;
    }
} 