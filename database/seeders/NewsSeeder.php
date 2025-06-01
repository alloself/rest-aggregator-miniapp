<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = Restaurant::all();
        $admin = User::first();

        // Новости от ресторанов
        foreach ($restaurants as $restaurant) {
            News::create([
                'title' => 'Новое меню в ' . $restaurant->name,
                'content' => 'Мы рады представить обновленное меню с сезонными блюдами и новыми вкусами.',
                'author_id' => $restaurant->id,
                'author_type' => Restaurant::class,
                'status' => 'published',
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }

        // Новости от администрации платформы
        if ($admin) {
            News::create([
                'title' => 'Добро пожаловать в наш агрегатор ресторанов!',
                'content' => 'Мы запускаем новую платформу для объединения лучших ресторанов города. Здесь вы найдете разнообразные кухни, интересные события и специальные предложения.',
                'author_id' => $admin->id,
                'author_type' => User::class,
                'status' => 'published',
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(45),
            ]);

            News::create([
                'title' => 'Новые функции платформы',
                'content' => 'Добавлены новые возможности: система лайков, персональные рекомендации и улучшенный поиск по категориям.',
                'author_id' => $admin->id,
                'author_type' => User::class,
                'status' => 'published',
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(15),
            ]);
        }
    }
}
