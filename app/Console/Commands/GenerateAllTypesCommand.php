<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateAllTypesCommand extends Command
{
    protected $signature = 'generate:all-types {--watch : Наблюдать за изменениями}';
    protected $description = 'Генерирует TypeScript типы для моделей и API Resources';

    public function handle(): int
    {
        $this->info('🚀 Начинаю генерацию всех TypeScript типов...');
        
        // Генерация типов моделей
        $this->info('📋 Генерирую типы моделей...');
        $exitCode = $this->call('model:typer');
        
        if ($exitCode !== 0) {
            $this->error('❌ Ошибка при генерации типов моделей');
            return self::FAILURE;
        }
        
        // Генерация типов ресурсов
        $this->info('🎯 Генерирую типы API ресурсов...');
        $exitCode = $this->call('generate:resource-types');
        
        if ($exitCode !== 0) {
            $this->error('❌ Ошибка при генерации типов API ресурсов');
            return self::FAILURE;
        }
        
        $this->info('✅ Все TypeScript типы успешно сгенерированы!');
        $this->line('');
        $this->line('📁 Файлы:');
        $this->line('   • resources/shared/types/models.d.ts (типы моделей)');
        $this->line('   • resources/shared/types/resources.d.ts (типы API ресурсов)');
        
        return self::SUCCESS;
    }
}
