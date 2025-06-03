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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique(); // SEO-friendly URL
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('telegram_bot_token'); // Encrypted storage
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by_admin_id');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('is_active');
            $table->index('created_by_admin_id');
            $table->index('slug'); // Для быстрого поиска по slug
            
            // Foreign key будет добавлен отдельной миграцией после создания users
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
