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
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['seasonal', 'cuisine', 'custom'])->default('custom');
            $table->string('color', 7)->nullable(); // Hex color
            $table->string('icon')->nullable();
            $table->uuid('parent_id')->nullable(); // Self-reference for hierarchy
            // Nested Set fields для kalnoy/nestedset
            $table->unsignedInteger('_lft')->default(0);
            $table->unsignedInteger('_rgt')->default(0);
            $table->unsignedInteger('depth')->default(0);
            $table->timestamps();
            
            // Indexes
            $table->index('type');
            $table->index('parent_id');
            // Nested Set indexes для оптимизации запросов
            $table->index(['_lft', '_rgt']);
            $table->index('depth');
            
            // Foreign keys
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
