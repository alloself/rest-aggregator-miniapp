<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class FileSeeder extends Seeder
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

        // Путь к папке с файлами сидов
        $seedFilesPath = database_path('seeders/files/images');
        
        // Убеждаемся, что директория для файлов существует
        Storage::disk('public')->makeDirectory('files');

        // Собираем все файлы из всех типов
        $allFiles = [];
        $allFiles = array_merge($allFiles, $this->getImagesFromDirectory($seedFilesPath, 'cover'));
        $allFiles = array_merge($allFiles, $this->getImagesFromDirectory($seedFilesPath, 'gallery'));
        $allFiles = array_merge($allFiles, $this->getImagesFromDirectory($seedFilesPath, 'telegram_avatar'));

        // Синхронизируем все файлы одним вызовом
        if (!empty($allFiles)) {
            $grettoRestaurant->syncImages($allFiles);
            $this->command->info('Добавлено ' . count($allFiles) . ' изображений для ресторана Gretto.');
        }

        $this->command->info('Файлы успешно добавлены для ресторана Gretto.');
    }

    /**
     * Получает изображения из указанной директории
     */
    private function getImagesFromDirectory(string $basePath, string $key): array
    {
        $directoryPath = $basePath . '/' . $key;
        
        if (!is_dir($directoryPath)) {
            $this->command->warn("Директория {$directoryPath} не найдена.");
            return [];
        }

        $files = glob($directoryPath . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
        
        if (empty($files)) {
            $this->command->warn("Файлы изображений не найдены в директории {$key}.");
            return [];
        }

        $order = 1;
        $copiedFiles = [];

        foreach ($files as $filePath) {
            $fileName = basename($filePath);
            
            // Пропускаем Zone.Identifier файлы
            if (str_contains($fileName, 'Zone.Identifier')) {
                continue;
            }

            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
            
            // Генерируем уникальное имя файла
            $uniqueFileName = uniqid() . '.' . $extension;
            $storagePath = 'files/' . $uniqueFileName;

            // Копируем файл в storage
            $fileContents = file_get_contents($filePath);
            Storage::disk('public')->put($storagePath, $fileContents);

            // Создаем запись в таблице files
            $file = File::create([
                'name' => $fileName,
                'url' => $storagePath,
                'extension' => $extension,
            ]);

            $copiedFiles[] = [
                'id' => $file->id,
                'pivot' => [
                    'key' => $key,
                    'order' => $order,
                ]
            ];

            $order++;
        }

        if (!empty($copiedFiles)) {
            $this->command->info("Подготовлено " . count($copiedFiles) . " изображений с ключом '{$key}'.");
        }

        return $copiedFiles;
    }
}