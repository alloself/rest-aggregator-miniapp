<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            RestaurantSeeder::class,
            MenuSeeder::class,
            DishSeeder::class,
            EventSeeder::class,
            NewsSeeder::class,
            AuthSeeder::class,
        ]);
    }
}
