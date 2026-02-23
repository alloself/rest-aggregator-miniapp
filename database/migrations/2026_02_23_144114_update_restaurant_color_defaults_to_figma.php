<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Обновление дефолтных цветов по макету Figma.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE restaurants MODIFY primary_color VARCHAR(7) DEFAULT "#FFFEFD" NULL');
            DB::statement('ALTER TABLE restaurants MODIFY secondary_color VARCHAR(7) DEFAULT "#EEEEEE" NULL');
            DB::statement('ALTER TABLE restaurants MODIFY tertiary_color VARCHAR(7) DEFAULT "#EBEBEB" NULL');
            DB::statement('ALTER TABLE restaurants MODIFY background_color VARCHAR(7) DEFAULT "#FFFEFB" NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE restaurants ALTER COLUMN primary_color SET DEFAULT \'#FFFEFD\'');
            DB::statement('ALTER TABLE restaurants ALTER COLUMN secondary_color SET DEFAULT \'#EEEEEE\'');
            DB::statement('ALTER TABLE restaurants ALTER COLUMN tertiary_color SET DEFAULT \'#EBEBEB\'');
            DB::statement('ALTER TABLE restaurants ALTER COLUMN background_color SET DEFAULT \'#FFFEFB\'');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE restaurants MODIFY primary_color VARCHAR(7) DEFAULT "#FFF8F2" NULL');
            DB::statement('ALTER TABLE restaurants MODIFY secondary_color VARCHAR(7) DEFAULT "#ECE2D5" NULL');
            DB::statement('ALTER TABLE restaurants MODIFY tertiary_color VARCHAR(7) DEFAULT "#ECE2D5" NULL');
            DB::statement('ALTER TABLE restaurants MODIFY background_color VARCHAR(7) DEFAULT "#FFFCF6" NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE restaurants ALTER COLUMN primary_color SET DEFAULT \'#FFF8F2\'');
            DB::statement('ALTER TABLE restaurants ALTER COLUMN secondary_color SET DEFAULT \'#ECE2D5\'');
            DB::statement('ALTER TABLE restaurants ALTER COLUMN tertiary_color SET DEFAULT \'#ECE2D5\'');
            DB::statement('ALTER TABLE restaurants ALTER COLUMN background_color SET DEFAULT \'#FFFCF6\'');
        }
    }
};
