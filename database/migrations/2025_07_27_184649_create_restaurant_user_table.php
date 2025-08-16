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
        Schema::create('restaurant_user', function (Blueprint $table) {
            $table->id();
            $table->uuid('restaurant_id');
            $table->uuid('user_id');
            $table->string('chat_id')->nullable();
            $table->string('chat_type')->nullable(); // private, group, supergroup, channel
            $table->timestamps();

            // Внешние ключи
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Уникальная связь пользователь-ресторан
            $table->unique(['restaurant_id', 'user_id']);

            // Индексы для производительности
            $table->index('restaurant_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_user');
    }
};
