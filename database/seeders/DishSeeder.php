<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dish;
use App\Models\Menu;
use App\Models\Category;

class DishSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = Menu::with('restaurant')->get();
        $categories = Category::all();

        foreach ($menus as $menu) {
            if ($menu->restaurant->name === 'Ресторан "Золотая рыбка"') {
                if ($menu->name === 'Основное меню') {
                    $dishes = [
                        [
                            'name' => 'Лосось на гриле',
                            'description' => 'Свежий лосось с овощами гриль и лимонным соусом',
                            'price' => 1850.00,
                            'is_available' => true,
                            'allergens' => ['рыба'],
                            'sort_order' => 1,
                        ],
                        [
                            'name' => 'Крем-суп из морепродуктов',
                            'description' => 'Нежный крем-суп с креветками, мидиями и кальмарами',
                            'price' => 890.00,
                            'is_available' => true,
                            'allergens' => ['морепродукты', 'молоко'],
                            'sort_order' => 2,
                        ],
                        [
                            'name' => 'Тирамису',
                            'description' => 'Классический итальянский десерт',
                            'price' => 450.00,
                            'is_available' => true,
                            'allergens' => ['яйца', 'молоко', 'глютен'],
                            'sort_order' => 3,
                        ],
                    ];

                    foreach ($dishes as $dishData) {
                        $dish = Dish::create(array_merge($dishData, ['menu_id' => $menu->id]));
                        
                        // Привязываем к категориям
                        if ($dishData['name'] === 'Лосось на гриле') {
                            $dish->categories()->attach([
                                $categories->where('slug', 'main-courses')->first()->id,
                                $categories->where('slug', 'fish-dishes')->first()->id,
                            ]);
                        } elseif ($dishData['name'] === 'Крем-суп из морепродуктов') {
                            $dish->categories()->attach([
                                $categories->where('slug', 'appetizers')->first()->id,
                            ]);
                        } elseif ($dishData['name'] === 'Тирамису') {
                            $dish->categories()->attach([
                                $categories->where('slug', 'desserts')->first()->id,
                                $categories->where('slug', 'italian')->first()->id,
                            ]);
                        }
                    }
                }
            } elseif ($menu->restaurant->name === 'Пиццерия "Мама Мия"') {
                if ($menu->name === 'Пицца') {
                    $dishes = [
                        [
                            'name' => 'Пицца Маргарита',
                            'description' => 'Томатный соус, моцарелла, базилик',
                            'price' => 750.00,
                            'is_available' => true,
                            'allergens' => ['глютен', 'молоко'],
                            'sort_order' => 1,
                        ],
                        [
                            'name' => 'Пицца Пепперони',
                            'description' => 'Томатный соус, моцарелла, пепперони',
                            'price' => 950.00,
                            'is_available' => true,
                            'allergens' => ['глютен', 'молоко'],
                            'sort_order' => 2,
                        ],
                    ];

                    foreach ($dishes as $dishData) {
                        $dish = Dish::create(array_merge($dishData, ['menu_id' => $menu->id]));
                        $dish->categories()->attach([
                            $categories->where('slug', 'main-courses')->first()->id,
                            $categories->where('slug', 'italian')->first()->id,
                        ]);
                    }
                }
            } elseif ($menu->restaurant->name === 'Суши-бар "Сакура"') {
                if ($menu->name === 'Суши и роллы') {
                    $dishes = [
                        [
                            'name' => 'Ролл Филадельфия',
                            'description' => 'Лосось, сливочный сыр, огурец',
                            'price' => 650.00,
                            'is_available' => true,
                            'allergens' => ['рыба', 'молоко'],
                            'sort_order' => 1,
                        ],
                        [
                            'name' => 'Суши с тунцом',
                            'description' => 'Свежий тунец на рисе',
                            'price' => 350.00,
                            'is_available' => true,
                            'allergens' => ['рыба'],
                            'sort_order' => 2,
                        ],
                    ];

                    foreach ($dishes as $dishData) {
                        $dish = Dish::create(array_merge($dishData, ['menu_id' => $menu->id]));
                        $dish->categories()->attach([
                            $categories->where('slug', 'main-courses')->first()->id,
                            $categories->where('slug', 'japanese')->first()->id,
                        ]);
                    }
                }
            }
        }
    }
}
