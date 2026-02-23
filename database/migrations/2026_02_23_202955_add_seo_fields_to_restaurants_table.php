<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Добавляет SEO и Open Graph поля в таблицу restaurants.
     */
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('yandex_metrica_code');
            }
            if (!Schema::hasColumn('restaurants', 'seo_description')) {
                $table->longText('seo_description')->nullable()->after('seo_title');
            }
            if (!Schema::hasColumn('restaurants', 'seo_keywords')) {
                $table->longText('seo_keywords')->nullable()->after('seo_description');
            }
            if (!Schema::hasColumn('restaurants', 'seo_robots')) {
                $table->string('seo_robots')->nullable()->after('seo_keywords');
            }
            if (!Schema::hasColumn('restaurants', 'seo_canonical_url')) {
                $table->string('seo_canonical_url')->nullable()->after('seo_robots');
            }
            if (!Schema::hasColumn('restaurants', 'og_title')) {
                $table->string('og_title')->nullable()->after('seo_canonical_url');
            }
            if (!Schema::hasColumn('restaurants', 'og_description')) {
                $table->longText('og_description')->nullable()->after('og_title');
            }
            if (!Schema::hasColumn('restaurants', 'og_image_url')) {
                $table->string('og_image_url')->nullable()->after('og_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $columns = [
                'seo_title',
                'seo_description',
                'seo_keywords',
                'seo_robots',
                'seo_canonical_url',
                'og_title',
                'og_description',
                'og_image_url',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('restaurants', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
