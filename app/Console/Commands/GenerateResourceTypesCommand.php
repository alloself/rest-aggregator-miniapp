<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Finder\Finder;

class GenerateResourceTypesCommand extends Command
{
    protected $signature = 'generate:resource-types {--output=resources/shared/types/resources.d.ts}';
    protected $description = 'Генерирует TypeScript типы для Laravel API Resources';

    public function __construct(private Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $outputPath = $this->option('output');
        $fullPath = base_path($outputPath);

        $this->info('Генерация TypeScript типов для API Resources...');

        // Найти все файлы ресурсов
        $finder = new Finder();
        $resourceFiles = $finder
            ->files()
            ->name('*Resource.php')
            ->in(app_path('Http/Resources'));

        $types = [];
        $imports = ['import type {'];
        $modelImports = [];
        $processedResourceNames = []; // Избегаем дублирования по имени

        foreach ($resourceFiles as $file) {
            $resourceClass = $this->getClassFromFile($file->getPathname());
            if (!$resourceClass) {
                continue;
            }

            $reflection = new ReflectionClass($resourceClass);
            $toArrayMethod = $reflection->getMethod('toArray');
            
            $resourceData = $this->analyzeToArrayMethod($toArrayMethod, $resourceClass);
            if ($resourceData) {
                $resourceName = Str::before(class_basename($resourceClass), 'Resource');
                
                // Пропускаем если уже обработали ресурс с таким именем
                // Приоритет отдаем ресурсам из папки Site
                $namespace = Str::before($resourceClass, '\\' . class_basename($resourceClass));
                $isSiteResource = str_contains($namespace, 'Site');
                
                if (!in_array($resourceName, $processedResourceNames) || $isSiteResource) {
                    // Если это Site ресурс, удаляем предыдущую версию
                    if ($isSiteResource && in_array($resourceName, $processedResourceNames)) {
                        $types = array_filter($types, function($type) use ($resourceName) {
                            return !str_contains($type, "interface {$resourceName}ResourceData");
                        });
                    }
                    
                    $types[] = $this->generateTypeDefinition($resourceName, $resourceData);
                    $processedResourceNames[] = $resourceName;
                    
                    // Добавить в импорты модель если она используется
                    if (!in_array($resourceName, $modelImports)) {
                        $modelImports[] = $resourceName;
                    }
                }
            }
        }

        // Построить содержимое файла
        $imports[] = '  ' . implode(',', $modelImports);
        $imports[] = "} from './models'";
        $imports[] = '';

        $content = implode("\n", $imports) . "\n";
        $content .= "// Автоматически сгенерированные типы для Laravel API Resources\n";
        $content .= "// Не редактируйте вручную - файл перезаписывается командой generate:resource-types\n\n";

        // Добавить вспомогательные типы
        $content .= $this->getHelperTypes() . "\n";

        $content .= implode("\n\n", $types);

        // Создать директорию если не существует
        $this->files->ensureDirectoryExists(dirname($fullPath));
        $this->files->put($fullPath, $content);

        $this->info("TypeScript типы сгенерированы в: {$outputPath}");
        
        return self::SUCCESS;
    }

    private function getClassFromFile(string $filePath): ?string
    {
        $content = $this->files->get($filePath);
        
        // Извлечь namespace и class name из файла
        preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches);
        preg_match('/class\s+(\w+)/', $content, $classMatches);
        
        if (!$namespaceMatches || !$classMatches) {
            return null;
        }
        
        return $namespaceMatches[1] . '\\' . $classMatches[1];
    }

    private function analyzeToArrayMethod(ReflectionMethod $method, string $className): ?array
    {
        // Получить содержимое метода toArray
        $startLine = $method->getStartLine();
        $endLine = $method->getEndLine();
        $source = file($method->getFileName());
        
        $methodContent = implode('', array_slice($source, $startLine - 1, $endLine - $startLine + 1));
        
        // Попытаться извлечь структуру return array
        if (preg_match('/return\s*\[(.*?)\];/s', $methodContent, $matches)) {
            return $this->parseReturnArray($matches[1], $className);
        }
        
        return null;
    }

    private function parseReturnArray(string $arrayContent, string $className): array
    {
        $fields = [];
        
        // Более продвинутый парсинг полей
        $content = trim($arrayContent);
        
        // Удаляем внешние скобки если есть
        if (str_starts_with($content, '[') && str_ends_with($content, ']')) {
            $content = substr($content, 1, -1);
        }
        
        // Разбиваем по элементам массива, учитывая вложенные массивы и функции
        $elements = $this->splitArrayElements($content);
        
        foreach ($elements as $element) {
            $element = trim($element);
            
            if (preg_match("/['\"](\w+)['\"]\s*=>\s*(.+)/s", $element, $matches)) {
                $fieldName = $matches[1];
                $fieldExpression = trim($matches[2]);
                
                $fields[$fieldName] = $this->determineFieldTypeFromExpression($fieldExpression, $fieldName, $className);
            }
        }
        
        return $fields;
    }

    private function splitArrayElements(string $content): array
    {
        $elements = [];
        $current = '';
        $depth = 0;
        $inString = false;
        $stringChar = '';
        $i = 0;
        $length = strlen($content);
        
        while ($i < $length) {
            $char = $content[$i];
            
            // Обработка строк
            if (!$inString && ($char === '"' || $char === "'")) {
                $inString = true;
                $stringChar = $char;
            } elseif ($inString && $char === $stringChar && ($i === 0 || $content[$i-1] !== '\\')) {
                $inString = false;
                $stringChar = '';
            }
            
            if (!$inString) {
                // Учитываем вложенность скобок и массивов
                if ($char === '(' || $char === '[') {
                    $depth++;
                } elseif ($char === ')' || $char === ']') {
                    $depth--;
                } elseif ($char === ',' && $depth === 0) {
                    // Это разделитель на верхнем уровне
                    $elements[] = trim($current);
                    $current = '';
                    $i++;
                    continue;
                }
            }
            
            $current .= $char;
            $i++;
        }
        
        if (trim($current) !== '') {
            $elements[] = trim($current);
        }
        
        return $elements;
    }

    private function determineFieldTypeFromExpression(string $expression, string $fieldName, string $className): string
    {
        // Более детальный анализ выражений
        $expression = trim($expression);
        
        // Удаляем лишние скобки
        if (str_starts_with($expression, '(') && str_ends_with($expression, ')')) {
            $expression = substr($expression, 1, -1);
        }
        
        // Анализируем различные паттерны Laravel ресурсов
        if (preg_match('/(\w+Resource)::collection\(\$this->whenLoaded\([\'"](\w+)[\'"]\)\)/', $expression, $matches)) {
            $resourceClass = $matches[1];
            $relationName = $matches[2];
            return $this->getResourceTypeFromClass($resourceClass) . '[]';
        }
        
        if (preg_match('/(\w+Resource)::collection\(\$this->(\w+)\)/', $expression, $matches)) {
            $resourceClass = $matches[1];
            return $this->getResourceTypeFromClass($resourceClass) . '[]';
        }
        
        if (preg_match('/(\w+Resource)::collection\((\$\w+)\)/', $expression, $matches)) {
            $resourceClass = $matches[1];
            return $this->getResourceTypeFromClass($resourceClass) . '[]';
        }
        
        if (preg_match('/new (\w+Resource)\(\$this->whenLoaded\([\'"](\w+)[\'"]\)\)/', $expression, $matches)) {
            $resourceClass = $matches[1];
            return $this->getResourceTypeFromClass($resourceClass);
        }
        
        // Специальная обработка pivot полей
        if (preg_match('/\$this->when\(isset\(\$this->pivot\),\s*fn\(\)\s*=>\s*\[/', $expression)) {
            return 'CategoryPivot';
        }
        
        // Обработка других when конструкций с массивами
        if (preg_match('/\$this->when\([^,]+,\s*fn\(\)\s*=>\s*\[(.*?)\]/', $expression, $matches)) {
            $arrayContent = $matches[1];
            if (str_contains($arrayContent, 'pivot->')) {
                return 'CategoryPivot';
            }
            // Если это просто массив значений - определяем по контексту
            return $this->getModelAttributeType($fieldName);
        }
        
        if (preg_match('/\$this->whenLoaded\([\'"](\w+)[\'"]\)/', $expression, $matches)) {
            $relationName = $matches[1];
            return $this->getRelationTypeByFieldName($relationName);
        }
        
        // Прямое обращение к атрибуту модели: $this->field_name
        if (preg_match('/^\$this->(\w+)$/', $expression, $matches)) {
            $attributeName = $matches[1];
            return $this->getModelAttributeType($attributeName);
        }
        
        // Если выражение содержит $this->field но не только это
        if (preg_match('/\$this->(\w+)/', $expression, $matches)) {
            $attributeName = $matches[1];
            // Если fieldName совпадает с attributeName, значит это прямое обращение
            if ($fieldName === $attributeName) {
                return $this->getModelAttributeType($attributeName);
            }
        }
        
        // Fallback на исходный метод
        return $this->determineFieldType($expression, $fieldName, $className);
    }

    private function getResourceTypeFromClass(string $resourceClass): string
    {
        $mapping = [
            'FileResource' => 'FileResourceData',
            'CategoryResource' => 'CategoryResourceData',
            'DishResource' => 'DishResourceData',
            'NewsResource' => 'NewsResourceData',
            'EventResource' => 'EventResourceData',
            'UserResource' => 'UserResourceData',
            'RestaurantResource' => 'RestaurantResourceData',
        ];
        
        return $mapping[$resourceClass] ?? 'string';
    }

    private function determineFieldType(string $line, string $fieldName, string $className): string
    {
        // Определить тип поля на основе содержимого строки
        if (str_contains($line, '::collection(')) {
            // Collection resource
            if (str_contains($line, 'FileResource::collection')) {
                return 'FileResourceData[]';
            } elseif (str_contains($line, 'CategoryResource::collection')) {
                return 'CategoryResourceData[]';
            } elseif (str_contains($line, 'DishResource::collection')) {
                return 'DishResourceData[]';
            } elseif (str_contains($line, 'NewsResource::collection')) {
                return 'NewsResourceData[]';
            } elseif (str_contains($line, 'EventResource::collection')) {
                return 'EventResourceData[]';
            } elseif (str_contains($line, 'UserResource::collection')) {
                return 'UserResourceData[]';
            }
            return $this->getModelAttributeType($fieldName) . '[]';
        } elseif (str_contains($line, 'Resource(')) {
            // Single resource
            if (str_contains($line, 'new RestaurantResource(') || str_contains($line, 'RestaurantResource(')) {
                return 'RestaurantResourceData';
            } elseif (str_contains($line, 'new CategoryResource(') || str_contains($line, 'CategoryResource(')) {
                return 'CategoryResourceData';
            } elseif (str_contains($line, 'new UserResource(') || str_contains($line, 'UserResource(')) {
                return 'UserResourceData';
            } elseif (str_contains($line, 'new FileResource(') || str_contains($line, 'FileResource(')) {
                return 'FileResourceData';
            }
            // Определяем по имени поля
            return $this->getResourceTypeByFieldName($fieldName);
        } elseif (str_contains($line, '$this->whenLoaded(')) {
            // Optional loaded relation
            $relationType = $this->getRelationTypeByFieldName($fieldName);
            return $relationType . ' | undefined';
        } elseif (str_contains($line, '$this->when(')) {
            // Conditional field
            if (str_contains($line, 'pivot')) {
                return 'CategoryPivot | undefined';
            }
            // Пытаемся определить тип по контексту
            if (preg_match('/\$this->when\([^,]+,\s*fn\(\)\s*=>\s*(.+?)\)/', $line, $matches)) {
                $expression = trim($matches[1]);
                if (str_contains($expression, '??')) {
                    return $this->getModelAttributeType($fieldName) . ' | null';
                }
            }
            return $this->getModelAttributeType($fieldName);
        } elseif (str_contains($line, '$this->' . $fieldName)) {
            // Direct model attribute
            return $this->getModelAttributeType($fieldName);
        }
        
        // Fallback - пытаемся определить по имени поля
        return $this->getModelAttributeType($fieldName);
    }

    private function getModelAttributeType(string $fieldName): string
    {
        // Специальные типы
        if ($fieldName === 'pivot') {
            return 'CategoryPivot';
        }
        
        if ($fieldName === 'working_hours') {
            return 'WorkingHours | null';
        }
        
        // Определить тип на основе имени поля
        $stringFields = ['id', 'name', 'slug', 'description', 'subtitle', 'address', 'phone', 'title', 'url', 'extension', 'email', 'username', 'avatar_url', 'first_name', 'last_name', 'middle_name', 'yandex_metrica_code', 'telegram_bot_token', 'welcome_message', 'category_id', 'restaurant_id', 'fileable_type', 'fileable_id', 'type', 'team_id', 'guard_name'];
        $stringNullableFields = ['subtitle', 'description', 'address', 'phone', 'email', 'username', 'avatar_url', 'last_name', 'middle_name', 'yandex_metrica_code', 'telegram_bot_token', 'welcome_message', 'key', 'team_id'];
        $numberFields = ['order', 'price', 'average_receipt'];
        $dateFields = ['created_at', 'updated_at', 'deleted_at', 'start_at', 'email_verified_at'];
        $booleanFields = ['is_published', 'is_closed'];
        
        if (in_array($fieldName, $stringFields)) {
            if (in_array($fieldName, $stringNullableFields)) {
                return 'string | null';
            }
            return 'string';
        } elseif (in_array($fieldName, $numberFields)) {
            return 'number | null';
        } elseif (in_array($fieldName, $dateFields)) {
            return 'Date | null';
        } elseif (in_array($fieldName, $booleanFields)) {
            return 'boolean';
        }
        
        // Если поле заканчивается на _id, скорее всего это string
        if (str_ends_with($fieldName, '_id')) {
            return 'string';
        }
        
        // Если поле заканчивается на _at, скорее всего это дата
        if (str_ends_with($fieldName, '_at')) {
            return 'Date | null';
        }
        
        // Если поле начинается с is_, скорее всего это boolean
        if (str_starts_with($fieldName, 'is_') || str_starts_with($fieldName, 'has_')) {
            return 'boolean';
        }
        
        return 'string'; // Более безопасный fallback чем any
    }

    private function getResourceTypeByFieldName(string $fieldName): string
    {
        $mapping = [
            'restaurant' => 'RestaurantResourceData',
            'category' => 'CategoryResourceData',
            'user' => 'UserResourceData',
            'file' => 'FileResourceData',
            'files' => 'FileResourceData[]',
            'images' => 'FileResourceData[]',
            'categories' => 'CategoryResourceData[]',
            'dishes' => 'DishResourceData[]',
            'news' => 'NewsResourceData[]',
            'events' => 'EventResourceData[]',
            'users' => 'UserResourceData[]',
        ];
        
        return $mapping[$fieldName] ?? $this->getModelAttributeType($fieldName);
    }

    private function getRelationTypeByFieldName(string $fieldName): string
    {
        $mapping = [
            'restaurant' => 'RestaurantResourceData',
            'category' => 'CategoryResourceData', 
            'user' => 'UserResourceData',
            'files' => 'FileResourceData[]',
            'images' => 'FileResourceData[]',
            'categories' => 'CategoryResourceData[]',
            'dishes' => 'DishResourceData[]',
            'news' => 'NewsResourceData[]',
            'events' => 'EventResourceData[]',
            'users' => 'UserResourceData[]',
            'parent' => 'CategoryResourceData',
            'children' => 'CategoryResourceData[]',
        ];
        
        return $mapping[$fieldName] ?? $this->getModelAttributeType($fieldName);
    }

    private function generateTypeDefinition(string $resourceName, array $fields): string
    {
        $definition = "export interface {$resourceName}ResourceData {\n";
        
        foreach ($fields as $fieldName => $fieldType) {
            // Более умная логика определения опциональности
            $isOptional = str_contains($fieldType, 'null') 
                || str_contains($fieldType, 'undefined') 
                || str_contains($fieldType, '[]')
                || in_array($fieldName, ['files', 'images', 'categories', 'dishes', 'news', 'events', 'users', 'children', 'parent', 'restaurant', 'category', 'pivot']);
            
            $optional = $isOptional ? '?' : '';
            
            // Убираем лишний " | undefined" если поле и так опциональное
            if ($optional && str_contains($fieldType, ' | undefined')) {
                $fieldType = str_replace(' | undefined', '', $fieldType);
            }
            
            $definition .= "  {$fieldName}{$optional}: {$fieldType}\n";
        }
        
        $definition .= "}";
        
        return $definition;
    }

    private function getHelperTypes(): string
    {
        return <<<'TYPESCRIPT'
// Вспомогательные типы для pivot полей
export interface CategoryPivot {
  id: string | null
  key: string | null
  order: number | null
}

// Рабочие часы ресторана
export interface WorkingHours {
  [day: string]: {
    open: string
    close: string
    is_closed: boolean
  } | null
}
TYPESCRIPT;
    }
}
