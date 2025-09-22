<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Dish;
use App\Models\File;
use Illuminate\Database\Seeder;

class DishSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::whereIn('slug', [
            'shef-rekomenduet', 'zavtraki', 'salaty', 'pasty', 'supy', 'goryachee', 'deserty', 'kofe', 'chaj',
        ])->get()->keyBy('slug');

        if ($categories->isEmpty()) {
            $this->command->error('Категории не найдены. Убедитесь, что CategorySeeder выполнен.');
            return;
        }

        $dishes = [
            // Шеф рекомендует
            [ 'name' => 'Тар-тар из тунца', 'slug' => 'tar-tar-iz-tunca', 'order' => 1, 'price' => 990, 'description' => 'Свежий тунец, каперсы, оливковое масло', 'category' => 'shef-rekomenduet' ],
            [ 'name' => 'Карпаччо из говядины', 'slug' => 'karpachcho-iz-govyadiny', 'order' => 2, 'price' => 1150, 'description' => 'Тонкие слайсы говядины, пармезан', 'category' => 'shef-rekomenduet' ],
            [ 'name' => 'Сибас на гриле', 'slug' => 'sibas-na-grile', 'order' => 3, 'price' => 1350, 'description' => 'Сибас с лимоном и зеленью', 'category' => 'shef-rekomenduet' ],
            [ 'name' => 'Паста с трюфелем', 'slug' => 'pasta-s-tryufelev', 'order' => 4, 'price' => 1490, 'description' => 'Паста с ароматом чёрного трюфеля', 'category' => 'shef-rekomenduet' ],
            // Завтраки
            [ 'name' => 'Омлет с сыром', 'slug' => 'omlet-s-syrom', 'order' => 1, 'price' => 420, 'description' => 'Классический омлет с сыром', 'category' => 'zavtraki' ],
            [ 'name' => 'Сырники со сметаной', 'slug' => 'syrniki-so-smetanoj', 'order' => 2, 'price' => 460, 'description' => 'Домашние сырники со сметаной', 'category' => 'zavtraki' ],
            // Салаты
            [ 'name' => 'Салат Цезарь', 'slug' => 'salat-cezar', 'order' => 1, 'price' => 690, 'description' => 'Классический салат с курицей', 'category' => 'salaty' ],
            [ 'name' => 'Греческий салат', 'slug' => 'grecheskij-salat', 'order' => 2, 'price' => 650, 'description' => 'Свежие овощи, фета, оливки', 'category' => 'salaty' ],
            // Пасты
            [ 'name' => 'Паста Карбонара', 'slug' => 'pasta-karbonara', 'order' => 1, 'price' => 740, 'description' => 'Панчетта, яйцо, пармезан', 'category' => 'pasty' ],
            [ 'name' => 'Паста Болоньезе', 'slug' => 'pasta-boloneze', 'order' => 2, 'price' => 760, 'description' => 'Соус болоньезе, пармезан', 'category' => 'pasty' ],
            // Супы
            [ 'name' => 'Минестроне', 'slug' => 'minestrone', 'order' => 1, 'price' => 520, 'description' => 'Овощной суп', 'category' => 'supy' ],
            [ 'name' => 'Томатный суп', 'slug' => 'tomatnyj-sup', 'order' => 2, 'price' => 540, 'description' => 'Суп из спелых томатов', 'category' => 'supy' ],
            // Горячее
            [ 'name' => 'Стейк Рибай', 'slug' => 'stejk-ribaj', 'order' => 1, 'price' => 1890, 'description' => 'Сочный стейк средней прожарки', 'category' => 'goryachee' ],
            [ 'name' => 'Курица по-гречески', 'slug' => 'kurica-po-grecheski', 'order' => 2, 'price' => 880, 'description' => 'Курица с прованскими травами', 'category' => 'goryachee' ],
            // Десерты
            [ 'name' => 'Тирамису', 'slug' => 'tiramisu', 'order' => 1, 'price' => 450, 'description' => 'Классический итальянский десерт', 'category' => 'deserty' ],
            [ 'name' => 'Панна-котта', 'slug' => 'panna-kotta', 'order' => 2, 'price' => 420, 'description' => 'Ванильная панна-котта с соусом', 'category' => 'deserty' ],
            // Напитки
            [ 'name' => 'Эспрессо', 'slug' => 'espresso', 'order' => 1, 'price' => 190, 'description' => 'Классический эспрессо', 'category' => 'kofe' ],
            [ 'name' => 'Капучино', 'slug' => 'kapuchino', 'order' => 2, 'price' => 240, 'description' => 'Кофе с молочной пеной', 'category' => 'kofe' ],
            [ 'name' => 'Чай зелёный', 'slug' => 'chaj-zelenyj', 'order' => 1, 'price' => 180, 'description' => 'Листовой зелёный чай', 'category' => 'chaj' ],
            [ 'name' => 'Чай чёрный', 'slug' => 'chaj-chernyj', 'order' => 2, 'price' => 180, 'description' => 'Крепкий чёрный чай', 'category' => 'chaj' ],
        ];

        foreach ($dishes as $row) {
            $category = $categories[$row['category']] ?? null;
            if (!$category) {
                continue;
            }

            /** @var Dish $dish */
            $dish = Dish::firstOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'order' => $row['order'],
                    'price' => $row['price'] ?? null,
                    'description' => $row['description'] ?? null,
                    'category_id' => $category->id,
                ]
            );

            // Прикрепим случайные изображения как у новостей/событий
            $files = File::inRandomOrder()->take(random_int(0, 2))->get();
            if ($files->isNotEmpty()) {
                $order = 1;
                $images = [];
                foreach ($files as $file) {
                    $images[] = [
                        'id' => $file->id,
                        'pivot' => [
                            'key' => 'image',
                            'order' => $order,
                        ],
                    ];
                    $order++;
                }
                $dish->syncImages($images);
            }
        }

        $this->command->info('Созданы блюда для категорий Gretto.');
    }
}


