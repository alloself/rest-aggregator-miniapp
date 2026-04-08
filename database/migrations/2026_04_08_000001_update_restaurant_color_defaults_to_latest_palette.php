<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Обновление дефолтной палитры ресторана.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE restaurants MODIFY primary_color VARCHAR(7) DEFAULT "#FFFBF7" NULL');
            DB::statement('ALTER TABLE restaurants MODIFY secondary_color VARCHAR(7) DEFAULT "#FAF6F0" NULL');
            DB::statement('ALTER TABLE restaurants MODIFY tertiary_color VARCHAR(7) DEFAULT "#EBE4DE" NULL');
            DB::statement('ALTER TABLE restaurants MODIFY background_color VARCHAR(7) DEFAULT "#FFFCF6" NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE restaurants ALTER COLUMN primary_color SET DEFAULT \'#FFFBF7\'');
            DB::statement('ALTER TABLE restaurants ALTER COLUMN secondary_color SET DEFAULT \'#FAF6F0\'');
            DB::statement('ALTER TABLE restaurants ALTER COLUMN tertiary_color SET DEFAULT \'#EBE4DE\'');
            DB::statement('ALTER TABLE restaurants ALTER COLUMN background_color SET DEFAULT \'#FFFCF6\'');
        }

        DB::table('restaurants')
            ->where(function ($query) {
                $query
                    ->where('primary_color', '#FFFEFD')
                    ->where('secondary_color', '#EEEEEE')
                    ->where('tertiary_color', '#EBEBEB')
                    ->where('background_color', '#FFFEFB');
            })
            ->orWhere(function ($query) {
                $query
                    ->where('primary_color', '#FFF8F2')
                    ->where('secondary_color', '#ECE2D5')
                    ->where('tertiary_color', '#ECE2D5')
                    ->where('background_color', '#FFFCF6');
            })
            ->update([
                'primary_color' => '#FFFBF7',
                'secondary_color' => '#FAF6F0',
                'tertiary_color' => '#EBE4DE',
                'background_color' => '#FFFCF6',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
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

        DB::table('restaurants')
            ->where('primary_color', '#FFFBF7')
            ->where('secondary_color', '#FAF6F0')
            ->where('tertiary_color', '#EBE4DE')
            ->where('background_color', '#FFFCF6')
            ->update([
                'primary_color' => '#FFFEFD',
                'secondary_color' => '#EEEEEE',
                'tertiary_color' => '#EBEBEB',
                'background_color' => '#FFFEFB',
            ]);
    }
};
