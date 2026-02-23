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
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('primary_color', 7)->default('#FFFEFD')->nullable()->after('og_image_url');
            $table->string('secondary_color', 7)->default('#EEEEEE')->nullable()->after('primary_color');
            $table->string('tertiary_color', 7)->default('#EBEBEB')->nullable()->after('secondary_color');
            $table->string('background_color', 7)->default('#FFFEFB')->nullable()->after('tertiary_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['primary_color', 'secondary_color', 'tertiary_color', 'background_color']);
        });
    }
};
