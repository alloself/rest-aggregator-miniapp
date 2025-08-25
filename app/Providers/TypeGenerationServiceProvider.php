<?php

namespace App\Providers;

use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class TypeGenerationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Автоматически генерировать типы после выполнения миграций
        Event::listen(MigrationsEnded::class, function (MigrationsEnded $event) {
            if ($this->app->environment('local')) {
                $this->app->make('log')->info('Автоматическая генерация TypeScript типов после миграций...');
                
                try {
                    Artisan::call('generate:all-types');
                    $this->app->make('log')->info('TypeScript типы успешно сгенерированы');
                } catch (\Exception $e) {
                    $this->app->make('log')->error('Ошибка генерации TypeScript типов: ' . $e->getMessage());
                }
            }
        });
    }
}
