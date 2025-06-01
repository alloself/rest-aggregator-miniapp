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
        Schema::create('collection_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('collection_id');
            $table->uuidMorphs('item'); // Polymorphic relation (dishes, events, news, restaurants)
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Unique constraint to prevent duplicate items in collection
            $table->unique(['collection_id', 'item_id', 'item_type']);
            
            // Indexes
            $table->index('collection_id');
            $table->index('sort_order');
            
            // Foreign keys
            $table->foreign('collection_id')->references('id')->on('curated_collections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_items');
    }
};
