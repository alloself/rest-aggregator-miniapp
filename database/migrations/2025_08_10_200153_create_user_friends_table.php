<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_friends', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id'); // Пользователь, который добавил друга
            $table->uuid('friend_id'); // Друг пользователя
            // Упрощенная схема: ключевой идентификатор Telegram и кэш-поля
            $table->unsignedBigInteger('friend_telegram_id')->nullable();
            $table->string('last_photo_file_id')->nullable();
            $table->string('last_photo_file_path')->nullable();
            $table->string('last_photo_url')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();

            // Индексы
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('friend_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'friend_id']);
            $table->index(['friend_telegram_id']);
            
            // Уникальность пары user_id + friend_id
            $table->unique(['user_id', 'friend_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_friends');
    }
};
