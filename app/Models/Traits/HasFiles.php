<?php

namespace App\Models\Traits;

use App\Models\File;
use App\Models\Pivot\Fileable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasFiles
{
    public function files(): MorphToMany
    {
        return $this->morphToMany(File::class, 'fileable')
            ->using(Fileable::class)
            ->wherePivot('type', 'file')
            ->withPivot('key', 'order');
    }

    public function syncFiles(array $values): void
    {
        // Получаем ID файлов до синхронизации для последующей проверки
        $previousFileIds = $this->files()->pluck('files.id')->toArray();
        
        $mapped = collect($values)->mapWithKeys(function ($file) {
            return [
                $file['id'] => [
                    'key' => $file['pivot']['key'] ?? null,
                    'order' => $file['pivot']['order'] ?? 0,
                    'type' => 'file',
                ],
            ];
        });

        $this->files()->sync($mapped->toArray());
        
        // Получаем ID файлов после синхронизации
        $currentFileIds = collect($values)->pluck('id')->toArray();
        
        // Находим файлы, которые были отсоединены
        $detachedFileIds = array_diff($previousFileIds, $currentFileIds);
        
        // Очищаем осиротевшие файлы
        $this->cleanupOrphanedFiles($detachedFileIds);
    }

    /**
     * Проверить и удалить файлы, которые больше не имеют связей
     */
    protected function cleanupOrphanedFiles(array $fileIds): void
    {
        if (empty($fileIds)) {
            return;
        }
        
        // Находим файлы, которые больше не имеют связей
        $orphanedFiles = File::whereIn('id', $fileIds)
            ->whereDoesntHave('fileables')
            ->get();
        
        // Удаляем осиротевшие файлы
        foreach ($orphanedFiles as $file) {
            $file->deleteEntity();
        }
    }
}
