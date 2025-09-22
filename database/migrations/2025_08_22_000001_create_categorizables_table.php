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
        Schema::create('categorizables', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();

            $table->uuid('category_id');
            $table->string('categorizable_type');
            $table->uuid('categorizable_id');

            $table->string('key')->nullable();
            $table->integer('order')->default(0);

            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->index(['categorizable_type', 'categorizable_id']);
            $table->index(['category_id']);
            $table->index(['key']);
            $table->unique(['category_id', 'categorizable_type', 'categorizable_id', 'key'], 'categorizables_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorizables');
    }
};


