@extends('layouts.app')

@section('title', 'Рестораны - Restaurant Aggregator')

@section('body-class', 'site-interface telegram-mini-app')

@push('styles')
    @vite(['resources/site/css/app.css'])
@endpush

@section('content')
    <!-- Site Vue Application Mount Point -->
    <div id="site-app" class="site-app-container">
        <!-- Fallback content для случаев когда JavaScript отключен -->
        <noscript>
            <div class="no-js-fallback">
                <div class="no-js-fallback__content">
                    <h1>Restaurant Aggregator Platform</h1>
                    <p>Для работы приложения необходимо включить JavaScript.</p>
                    <p>Please enable JavaScript to use this application.</p>
                </div>
            </div>
        </noscript>
        
        <!-- Loading placeholder для Vue app -->
        <div id="site-loading" class="site-loading">
            <div class="site-loading__content">
                <div class="site-loading__logo">
                    <svg width="60" height="60" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <!-- Restaurant icon -->
                        <circle cx="50" cy="50" r="45" fill="var(--color-accent, rgba(200,228,155,0.35))" />
                        <path d="M25 40 L50 25 L75 40 L75 75 L25 75 Z" fill="var(--color-primary, #000000)" />
                        <rect x="35" y="45" width="30" height="3" fill="var(--color-white, #ffffff)" />
                        <rect x="35" y="55" width="30" height="3" fill="var(--color-white, #ffffff)" />
                        <rect x="35" y="65" width="30" height="3" fill="var(--color-white, #ffffff)" />
                    </svg>
                </div>
                <h2>Restaurant Aggregator</h2>
                <p>Загружаем рестораны...</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/site/ts/main.ts'])
    
    <script>
        // Site-specific initialization
        document.addEventListener('DOMContentLoaded', function() {
            // Site interface loading
            
            // Telegram Web App specific setup for site
            if (window.TelegramWebApp) {
                const tg = window.TelegramWebApp;
                
                // Version compatibility helper
                const isVersionSupported = (requiredVersion) => {
                    if (!tg.version) return false;
                    return parseFloat(tg.version) >= parseFloat(requiredVersion);
                };
                
                // Site-specific Telegram setup
                if (isVersionSupported('6.1') && typeof tg.setBackgroundColor === 'function') {
                    tg.setBackgroundColor('#ffffff');
                }
                
                // Main button configuration for restaurant actions
                if (tg.MainButton) {
                    tg.MainButton.text = 'Выбрать ресторан';
                    tg.MainButton.color = '#000000';
                    tg.MainButton.textColor = '#ffffff';
                    // MainButton будет показан/скрыт из Vue компонентов
                }
                
                // Back button handler (supported from version 6.1+)
                if (isVersionSupported('6.1') && tg.BackButton) {
                    tg.BackButton.onClick(() => {
                        // Обработка возврата на предыдущую страницу
                        window.dispatchEvent(new CustomEvent('telegram-back-button'));
                    });
                }
                
                // Settings button for user preferences (supported from version 6.1+)
                if (isVersionSupported('6.1') && tg.SettingsButton) {
                    tg.SettingsButton.onClick(() => {
                        window.dispatchEvent(new CustomEvent('telegram-settings-button'));
                    });
                }
                
                // Handle viewport changes
                tg.onEvent('viewportChanged', function() {
                    // Viewport changed
                    
                    // Notify Vue app about viewport changes
                    window.dispatchEvent(new CustomEvent('telegram-viewport-changed', {
                        detail: {
                            height: tg.viewportHeight,
                            stableHeight: tg.viewportStableHeight
                        }
                    }));
                });
                
                // Telegram Web App configured for site interface
            }
            
            // Analytics initialization (if needed)
            @if(config('app.env') === 'production' && config('services.analytics.enabled'))
            // Add analytics tracking here
            gtag('config', '{{ config('services.analytics.site_id') }}', {
                page_title: 'Restaurant Aggregator - Рестораны',
                page_location: window.location.href
            });
            @endif
        });
        
        // Global error handler for Vue app
        window.addEventListener('unhandledrejection', function(event) {
            // Handle unhandled promise rejection
            
            // Send to error tracking service if available
            @if(config('app.env') === 'production' && config('services.sentry.enabled'))
            if (window.Sentry) {
                window.Sentry.captureException(event.reason);
            }
            @endif
        });
        
        // Expose site configuration to Vue app
        window.SiteConfig = {
            apiBaseUrl: '{{ config('app.url') }}/api/v1',
            environment: '{{ config('app.env') }}',
            telegramBotUsername: '{{ config('telegram.bot_username') }}',
            features: {
                favorites: {{ config('features.favorites', 'true') }},
                ratings: {{ config('features.ratings', 'true') }},
                reservations: {{ config('features.reservations', 'true') }}
            }
        };
    </script>
@endpush

@push('head')
    <!-- Open Graph meta для социальных сетей -->
    <meta property="og:title" content="Restaurant Aggregator - Найдите лучшие рестораны">
    <meta property="og:description" content="Откройте для себя лучшие рестораны города с персональными рекомендациями">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/og-site-preview.jpg') }}">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Restaurant Aggregator">
    <meta name="twitter:description" content="Лучшие рестораны города в одном приложении">
    
    <!-- Telegram Mini App meta -->
    <meta name="telegram:card" content="app">
    <meta name="telegram:site" content="restaurant_aggregator_bot">
    
    <!-- Structured data для поисковых систем -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "Restaurant Aggregator",
        "description": "Платформа для поиска и бронирования ресторанов",
        "url": "{{ config('app.url') }}",
        "applicationCategory": "Food & Drink",
        "operatingSystem": "Web Browser, Telegram",
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "RUB"
        }
    }
    </script>
@endpush

{{-- CSS Override для Site интерфейса --}}
@push('styles')
<style>
    /* Site-specific styles */
    .site-app-container {
        min-height: 100vh;
        min-height: 100dvh;
        background-color: var(--color-background, #ffffff);
        position: relative;
    }
    
    .no-js-fallback {
        padding: var(--spacing-2xl, 24px);
        text-align: center;
        min-height: 50vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .no-js-fallback__content {
        max-width: 400px;
    }
    
    .site-loading {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--color-background, #ffffff);
        z-index: 1000;
    }
    
    .site-loading__content {
        text-align: center;
        padding: var(--spacing-xl, 20px);
    }
    
    .site-loading__logo {
        margin-bottom: var(--spacing-lg, 16px);
        animation: pulse 2s infinite;
    }
    
    .site-loading__content h2 {
        margin: var(--spacing-md, 12px) 0;
        font-size: var(--font-size-xl, 20px);
        font-weight: var(--font-weight-semibold, 600);
        color: var(--color-text-primary, #000000);
    }
    
    .site-loading__content p {
        margin: 0;
        font-size: var(--font-size-sm, 14px);
        color: var(--color-text-secondary, #1C1C1E);
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    /* Hide site loading when Vue app is mounted */
    .site-app-container.mounted .site-loading {
        display: none;
    }
</style>
@endpush