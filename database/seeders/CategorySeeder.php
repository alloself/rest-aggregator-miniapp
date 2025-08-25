<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurant = Restaurant::where('slug', 'gretto')->first();

        if (!$restaurant) {
            $this->command->error('Ресторан Gretto не найден. Убедитесь, что RestaurantSeeder выполнен.');
            return;
        }

        $categories = [
            [
                'name' => 'Шеф рекомендует',
                'slug' => 'shef-rekomenduet',
                'order' => 0,
                'pivot' => [
                    'key' => 'chief_recommendations',
                ],
            ],
            [
                'name' => 'Завтраки',
                'slug' => 'zavtraki',
                'order' => 1,
            ],
            [
                'name' => 'Салаты',
                'slug' => 'salaty',
                'order' => 2,
            ],
            [
                'name' => 'Пасты',
                'slug' => 'pasty',
                'order' => 3,
            ],
            [
                'name' => 'Супы',
                'slug' => 'supy',
                'order' => 4,
            ],
            [
                'name' => 'Горячее',
                'slug' => 'goryachee',
                'order' => 5,
            ],
            [
                'name' => 'Десерты',
                'slug' => 'deserty',
                'order' => 6,
            ],
            [
                'name' => 'Напитки',
                'slug' => 'napitki',
                'order' => 7,
                'children' => [
                    [
                        'name' => 'Кофе',
                        'slug' => 'kofe',
                        'order' => 1,
                    ],
                    [
                        'name' => 'Чай',
                        'slug' => 'chaj',
                        'order' => 2,
                    ],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $children = $catData['children'] ?? [];
            unset($catData['children']);

            /** @var Category $category */
            $category = Category::firstOrCreate(
                ['slug' => $catData['slug']],
                [
                    'name' => $catData['name'],
                    'order' => $catData['order'] ?? 0,
                ]
            );

            // Привязать категорию к ресторану через morph pivot
            $category->syncRestaurants([
                [
                    'restaurant_id' => $restaurant->id,
                    'order' => $catData['order'] ?? 0,
                    'key' => $catData['pivot']['key'] ?? null,
                ],
            ]);

            // Дочерние категории (если есть)
            foreach ($children as $childData) {
                /** @var Category $child */
                $child = Category::firstOrCreate(
                    ['slug' => $childData['slug']],
                    [
                        'name' => $childData['name'],
                        'order' => $childData['order'] ?? 0,
                        'parent_id' => $category->id,
                    ]
                );

                $child->syncRestaurants([
                    [
                        'restaurant_id' => $restaurant->id,
                        'order' => $childData['order'] ?? 0,
                    ],
                ]);
            }
        }

        $this->command->info('Категории (включая дочерние) созданы и привязаны к ресторану Gretto.');
    }
}


