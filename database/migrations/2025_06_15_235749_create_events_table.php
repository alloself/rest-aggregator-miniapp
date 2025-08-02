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
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('slug');
            $table->string('title');
            $table->string('subtitle');
            $table->datetime('start_at');
            $table->string('price');
            $table->longText('description');

            $table->uuid('restaurant_id');
            $table->foreign('restaurant_id')->references('id')->on('restaurants');

            $table->integer('order')->default(0);


            $table->index(['slug', 'restaurant_id']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
