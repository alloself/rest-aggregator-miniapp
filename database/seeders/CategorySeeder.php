<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Основные категории кухни
        $cuisineCategories = [
            ['name' => 'Европейская кухня', 'slug' => 'european', 'type' => 'cuisine', 'color' => '#3B82F6'],
            ['name' => 'Азиатская кухня', 'slug' => 'asian', 'type' => 'cuisine', 'color' => '#EF4444'],
            ['name' => 'Итальянская кухня', 'slug' => 'italian', 'type' => 'cuisine', 'color' => '#10B981'],
            ['name' => 'Японская кухня', 'slug' => 'japanese', 'type' => 'cuisine', 'color' => '#F59E0B'],
        ];

        foreach ($cuisineCategories as $category) {
            Category::create($category);
        }

        // Сезонные категории
        $seasonalCategories = [
            ['name' => 'Летние блюда', 'slug' => 'summer-dishes', 'type' => 'seasonal', 'color' => '#FBBF24'],
            ['name' => 'Зимние блюда', 'slug' => 'winter-dishes', 'type' => 'seasonal', 'color' => '#60A5FA'],
            ['name' => 'Новогоднее меню', 'slug' => 'new-year-menu', 'type' => 'seasonal', 'color' => '#F87171'],
            ['name' => 'Весенние новинки', 'slug' => 'spring-novelties', 'type' => 'seasonal', 'color' => '#34D399'],
        ];

        foreach ($seasonalCategories as $category) {
            Category::create($category);
        }

        // Кастомные категории
        $customCategories = [
            ['name' => 'Закуски', 'slug' => 'appetizers', 'type' => 'custom', 'color' => '#8B5CF6'],
            ['name' => 'Основные блюда', 'slug' => 'main-courses', 'type' => 'custom', 'color' => '#EC4899'],
            ['name' => 'Десерты', 'slug' => 'desserts', 'type' => 'custom', 'color' => '#F97316'],
            ['name' => 'Напитки', 'slug' => 'beverages', 'type' => 'custom', 'color' => '#06B6D4'],
            ['name' => 'Вегетарианские блюда', 'slug' => 'vegetarian', 'type' => 'custom', 'color' => '#84CC16'],
            ['name' => 'Острые блюда', 'slug' => 'spicy', 'type' => 'custom', 'color' => '#DC2626'],
        ];

        foreach ($customCategories as $category) {
            Category::create($category);
        }

        // Создаем подкатегории для основных блюд
        $mainCoursesCategory = Category::where('slug', 'main-courses')->first();
        if ($mainCoursesCategory) {
            $subCategories = [
                ['name' => 'Мясные блюда', 'slug' => 'meat-dishes', 'type' => 'custom', 'color' => '#991B1B', 'parent_id' => $mainCoursesCategory->id],
                ['name' => 'Рыбные блюда', 'slug' => 'fish-dishes', 'type' => 'custom', 'color' => '#1E40AF', 'parent_id' => $mainCoursesCategory->id],
                ['name' => 'Паста', 'slug' => 'pasta', 'type' => 'custom', 'color' => '#92400E', 'parent_id' => $mainCoursesCategory->id],
            ];

            foreach ($subCategories as $category) {
                Category::create($category);
            }
        }
    }
}
