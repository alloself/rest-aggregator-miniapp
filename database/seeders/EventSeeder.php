<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Restaurant;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = Restaurant::all();

        foreach ($restaurants as $restaurant) {
            if ($restaurant->name === 'Ресторан "Золотая рыбка"') {
                Event::create([
                    'restaurant_id' => $restaurant->id,
                    'title' => 'Дегустация вин',
                    'description' => 'Вечер дегустации французских вин с сомелье',
                    'start_date' => Carbon::now()->addDays(7)->setTime(19, 0),
                    'end_date' => Carbon::now()->addDays(7)->setTime(22, 0),
                    'max_participants' => 20,
                    'price' => 2500.00,
                    'status' => 'published',
                ]);

                Event::create([
                    'restaurant_id' => $restaurant->id,
                    'title' => 'Мастер-класс по приготовлению суши',
                    'description' => 'Научитесь готовить суши от профессионального шеф-повара',
                    'start_date' => Carbon::now()->addDays(14)->setTime(15, 0),
                    'end_date' => Carbon::now()->addDays(14)->setTime(18, 0),
                    'max_participants' => 12,
                    'price' => 3500.00,
                    'status' => 'published',
                ]);
            } elseif ($restaurant->name === 'Пиццерия "Мама Мия"') {
                Event::create([
                    'restaurant_id' => $restaurant->id,
                    'title' => 'Итальянский вечер',
                    'description' => 'Живая музыка и традиционные итальянские блюда',
                    'start_date' => Carbon::now()->addDays(5)->setTime(20, 0),
                    'end_date' => Carbon::now()->addDays(5)->setTime(23, 0),
                    'max_participants' => 50,
                    'price' => null,
                    'status' => 'published',
                ]);
            } elseif ($restaurant->name === 'Суши-бар "Сакура"') {
                Event::create([
                    'restaurant_id' => $restaurant->id,
                    'title' => 'Фестиваль японской кухни',
                    'description' => 'Специальное меню и чайная церемония',
                    'start_date' => Carbon::now()->addDays(10)->setTime(18, 0),
                    'end_date' => Carbon::now()->addDays(10)->setTime(21, 0),
                    'max_participants' => 30,
                    'price' => 1800.00,
                    'status' => 'published',
                ]);
            }
        }
    }
}
