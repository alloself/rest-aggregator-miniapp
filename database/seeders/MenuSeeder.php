<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Restaurant;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = Restaurant::all();

        foreach ($restaurants as $restaurant) {
            if ($restaurant->name === 'Ресторан "Золотая рыбка"') {
                Menu::create([
                    'restaurant_id' => $restaurant->id,
                    'name' => 'Основное меню',
                    'description' => 'Авторские блюда от шеф-повара',
                    'is_active' => true,
                    'sort_order' => 1,
                ]);

                Menu::create([
                    'restaurant_id' => $restaurant->id,
                    'name' => 'Винная карта',
                    'description' => 'Изысканные вина со всего мира',
                    'is_active' => true,
                    'sort_order' => 2,
                ]);
            } elseif ($restaurant->name === 'Пиццерия "Мама Мия"') {
                Menu::create([
                    'restaurant_id' => $restaurant->id,
                    'name' => 'Пицца',
                    'description' => 'Традиционная итальянская пицца',
                    'is_active' => true,
                    'sort_order' => 1,
                ]);

                Menu::create([
                    'restaurant_id' => $restaurant->id,
                    'name' => 'Паста и ризотто',
                    'description' => 'Домашняя паста и ароматные ризотто',
                    'is_active' => true,
                    'sort_order' => 2,
                ]);
            } elseif ($restaurant->name === 'Суши-бар "Сакура"') {
                Menu::create([
                    'restaurant_id' => $restaurant->id,
                    'name' => 'Суши и роллы',
                    'description' => 'Свежие суши и роллы',
                    'is_active' => true,
                    'sort_order' => 1,
                ]);

                Menu::create([
                    'restaurant_id' => $restaurant->id,
                    'name' => 'Горячие блюда',
                    'description' => 'Традиционные японские горячие блюда',
                    'is_active' => true,
                    'sort_order' => 2,
                ]);
            }
        }
    }
}
