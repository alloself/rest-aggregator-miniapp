# Deployment Strategy - Restaurant Aggregator Platform

## Development vs Production Environment Differences

### 🐳 **Development (Docker)**
```yaml
# docker-compose.yml
services:
  app:
    build: ./docker/php
    volumes:
      - .:/var/www/html
      - ./storage:/var/www/html/storage
  
  mysql:
    image: mysql:8.0
    volumes:
      - mysql_data:/var/lib/mysql
  
  redis:
    image: redis:alpine
    volumes:
      - redis_data:/data
```

### 🌐 **Production (Standard Hosting)**
```bash
# Обычный shared hosting или VPS
- cPanel/DirectAdmin или SSH доступ
- PHP 8.2+ с расширениями
- MySQL database
- File manager для uploads
- Composer через SSH или web interface
```

## File Storage Strategy

### 📁 **Development Storage (Docker Volumes)**
```bash
# Docker volume mapping
./storage/app/restaurants/ -> /var/www/html/storage/app/restaurants/
./public/storage/ -> /var/www/html/public/storage/ (symlink)

# Images accessible via:
http://localhost:8080/storage/restaurants/{uuid}/dishes/image.jpg
```

### 📁 **Production Storage (Standard Hosting)**
```bash
# Regular file system
/home/user/public_html/storage/app/restaurants/
/home/user/public_html/public/storage/ -> ../storage/app/public (symlink)

# Images accessible via:
https://yourdomain.com/storage/restaurants/{uuid}/dishes/image.jpg
```

## Database Configuration

### 🗄️ **Development Database (Docker)**
```env
DB_HOST=mysql          # Docker service name
DB_PORT=3306
DB_DATABASE=rest_aggregator
DB_USERNAME=sail
DB_PASSWORD=password
```

### 🗄️ **Production Database (Hosting)**
```env
DB_HOST=localhost      # или IP хостинга
DB_PORT=3306
DB_DATABASE=user_restaurant_db
DB_USERNAME=user_dbuser
DB_PASSWORD=secure_production_password
```

## Caching Strategy

### ⚡ **Development (Docker Redis)**
```env
CACHE_DRIVER=redis
REDIS_HOST=redis       # Docker service
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### ⚡ **Production (Hosting with fallbacks)**
```env
# Если Redis доступен
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1

# Fallback если Redis недоступен
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database
```

## Deployment Process

### 🚀 **Development Workflow**
```bash
# Start development environment
make up
make npm-install
make dev

# Database setup
make shell
php artisan migrate:fresh --seed
php artisan storage:link

# Frontend development
npm run dev  # Multiple Vite servers
```

### 🚀 **Production Deployment**
```bash
# 1. Code deployment
git pull origin main
composer install --no-dev --optimize-autoloader

# 2. Frontend build
npm ci
npm run build

# 3. Laravel optimization
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Storage setup
php artisan storage:link

# 5. Database migration
php artisan migrate --force

# 6. Clear application cache
php artisan cache:clear
php artisan queue:restart
```

## Configuration Files

### ⚙️ **Development Configuration**
```php
// config/filesystems.php
'default' => env('FILESYSTEM_DISK', 'local'),

'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
        'serve' => true,
    ],
]
```

### ⚙️ **Production Configuration**
```php
// config/filesystems.php  
'default' => env('FILESYSTEM_DISK', 'public'),

'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
]
```

## Environment Variables Templates

### 📄 **.env.example (Development)**
```env
APP_NAME="Restaurant Aggregator"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

# Docker Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=rest_aggregator
DB_USERNAME=sail
DB_PASSWORD=password

# Docker Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
CACHE_DRIVER=redis
SESSION_DRIVER=redis

# Storage
FILESYSTEM_DISK=local

# Vite Development
VITE_DEV_SERVER_HOST=0.0.0.0
VITE_APP_URL=${APP_URL}
```

### 📄 **.env.production.example**
```env
APP_NAME="Restaurant Aggregator"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Production Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=production_db_name
DB_USERNAME=production_db_user
DB_PASSWORD=secure_production_password

# Production Cache (with fallbacks)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
CACHE_DRIVER=redis
SESSION_DRIVER=file

# Production Storage
FILESYSTEM_DISK=public

# Security
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SAME_SITE=strict

# Performance
QUEUE_CONNECTION=database
LOG_CHANNEL=daily
LOG_LEVEL=error

# Telegram
TELEGRAM_BOT_TOKEN=your_platform_bot_token
```

## Image Processing Differences

### 🖼️ **Development Image Handling**
```php
// Development - простое хранение
class ImageService {
    public function store(UploadedFile $file, Restaurant $restaurant): string
    {
        // Direct storage в Docker volume
        return $file->store("restaurants/{$restaurant->uuid}/dishes");
    }
}
```

### 🖼️ **Production Image Handling**
```php
// Production - с оптимизацией
class ImageService {
    public function store(UploadedFile $file, Restaurant $restaurant): string
    {
        $path = "restaurants/{$restaurant->uuid}/dishes";
        
        // Store original
        $originalPath = $file->store($path, 'public');
        
        // Generate optimized versions
        $this->generateThumbnail($originalPath);
        $this->compressForWeb($originalPath);
        
        return $originalPath;
    }
}
```

## SSL & Security

### 🔒 **Development (Docker)**
```bash
# Self-signed certificates через nginx
ssl_certificate /etc/nginx/ssl/cert.pem;
ssl_certificate_key /etc/nginx/ssl/key.pem;
```

### 🔒 **Production (Hosting)**
```bash
# Let's Encrypt или хостинг SSL
# Автоматическое обновление через cPanel/hosting panel
# HTTPS redirect в .htaccess
```

## Monitoring & Logging

### 📊 **Development Logging**
```env
LOG_CHANNEL=stack
LOG_LEVEL=debug
LOG_DEPRECATIONS_CHANNEL=null
```

### 📊 **Production Logging**
```env
LOG_CHANNEL=daily
LOG_LEVEL=error
LOG_DEPRECATIONS_CHANNEL=null

# Telegram error notifications
TELEGRAM_ERROR_CHAT_ID=your_admin_chat_id
```

## Backup Strategy

### 💾 **Development Backup**
```bash
# Docker volume backup
docker-compose exec mysql mysqldump -u sail -p rest_aggregator > backup.sql
tar -czf storage_backup.tar.gz ./storage/
```

### 💾 **Production Backup**
```bash
# Automated backups через hosting cPanel
# Database: Daily MySQL dumps
# Files: Weekly storage/ folder backup
# Git: Automatic repository backup
```

This deployment strategy ensures smooth transition from Docker development to standard hosting production while maintaining consistency and performance. 