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
        Schema::create('dishes', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('slug');
            $table->string('name');
            $table->longText('description')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->uuid('category_id');
            $table->foreign('category_id')->references('id')->on('categories');

            $table->integer('order')->default(0);


            $table->index(['slug', 'category_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dishes');
    }
};
