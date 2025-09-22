<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\Restaurant;
use App\Models\File;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Находим ресторан Gretto
        $grettoRestaurant = Restaurant::where('slug', 'gretto')->first();

        if (!$grettoRestaurant) {
            $this->command->error('Ресторан Gretto не найден. Убедитесь, что RestaurantSeeder выполнен.');
            return;
        }

        // Создаем новости для ресторана Gretto (контент в HTML для WYSIWYG)
        $newsData = [
            [
                'title' => 'Открытие летней террасы',
                'slug' => 'otkrytie-letnej-terrasy',
                'description' => <<<HTML
<h3>Открытие летней террасы</h3>
<p>С радостью сообщаем, что в ресторане <strong>Gretto</strong> открылась уютная летняя терраса! Наслаждайтесь изысканными блюдами на свежем воздухе в окружении зелени.</p>
<ul>
  <li>Режим работы: <strong>ежедневно с 12:00 до 23:00</strong></li>
  <li>Комфортная посадка, пледы и обогреватели при прохладной погоде</li>
  <li>Бронирование доступно по телефону и через сайт</li>
  <li>Специальное террасное меню и летние коктейли</li>
  </ul>
HTML,
                'order' => 1,
            ],
            [
                'title' => 'Новое сезонное меню',
                'slug' => 'novoe-sezonnoe-menu',
                'description' => <<<HTML
<h3>Новое сезонное меню</h3>
<p>Встречайте обновлённое <em>весенне-летнее</em> меню! Мы подготовили блюда из свежих сезонных продуктов.</p>
<p><strong>Рекомендуем попробовать:</strong></p>
<ul>
  <li>Лёгкие салаты с молодой зеленью</li>
  <li>Освежающие супы-пюре</li>
  <li>Блюда на гриле</li>
  <li>Авторские десерты: ванильная панна-котта с ягодным соусом</li>
  <li>Фирменный тартар из тунца</li>
</ul>
HTML,
                'order' => 2,
            ],
            [
                'title' => 'Мастер-класс от шеф-повара',
                'slug' => 'master-klass-ot-shef-povara',
                'description' => <<<HTML
<h3>Мастер-класс: «Секреты итальянской пасты»</h3>
<p><strong>15 июня, 18:00</strong> — наш шеф-повар Марко Росси научит готовить домашнюю пасту с нуля и поделится секретами идеального соуса карбонара.</p>
<p>
  Количество мест ограничено — <strong>12 участников</strong>.<br/>
  Стоимость: <strong>3500 ₽</strong> (включает ужин).
</p>
HTML,
                'order' => 3,
            ],
            [
                'title' => 'Винная дегустация',
                'slug' => 'vinnaya-degustaciya',
                'description' => <<<HTML
<h3>Пятничные винные дегустации</h3>
<p>Каждую пятницу в <strong>19:00</strong> присоединяйтесь к дегустациям с нашим сомелье.</p>
<ul>
  <li>5 сортов вин из разных регионов</li>
  <li>Закуски от шеф-повара</li>
  <li>Обзор терруара и сочетаний с блюдами</li>
</ul>
<p><em>Предварительная запись обязательна.</em></p>
HTML,
                'order' => 4,
            ],
            [
                'title' => 'Корпоративные мероприятия',
                'slug' => 'korporativnye-meropriyatiya',
                'description' => <<<HTML
<h3>Корпоративные мероприятия в Gretto</h3>
<p>Идеальное место для деловых встреч, презентаций и торжественных ужинов.</p>
<ul>
  <li>Отдельный зал на <strong>40 персон</strong></li>
  <li>Современное аудио/видео-оборудование</li>
  <li>Специальное бизнес-меню</li>
  <li>Организация «под ключ» с учётом ваших задач</li>
</ul>
HTML,
                'order' => 5,
            ],
            [
                'title' => 'Семейные воскресенья',
                'slug' => 'semejnye-voskresnya',
                'description' => <<<HTML
<h3>Семейные воскресенья</h3>
<p>Каждое воскресенье — особая программа для семей с детьми.</p>
<ul>
  <li>Специальное детское меню</li>
  <li>Кулинарные мастер-классы для детей</li>
  <li><strong>Скидка 15%</strong> на основные блюда для семей</li>
  <li>Время: <strong>12:00–16:00</strong></li>
</ul>
HTML,
                'order' => 6,
            ],
        ];

        foreach ($newsData as $news) {
            $newsEntity = News::create([
                'title' => $news['title'],
                'slug' => $news['slug'],
                'description' => $news['description'],
                'order' => $news['order'],
                'restaurant_id' => $grettoRestaurant->id,
            ]);

            // Прикрепляем 1-2 случайных изображения к новости
            $files = File::inRandomOrder()->take(random_int(1, 2))->get();

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

                $newsEntity->syncImages($images);
            }
        }

        $this->command->info('Создано ' . count($newsData) . ' новостей для ресторана Gretto.');
    }
}