<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Telegram Mini App Meta -->
    <meta name="telegram-bot" content="restaurant_aggregator_bot">
    <meta name="telegram-miniapp" content="yes">
    
    <title>@yield('title', 'Restaurant Aggregator Platform')</title>
    
    <!-- Preconnect to external resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Telegram Web App Script -->
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    
    <!-- Interface-specific assets -->
    @stack('styles')
    
    <!-- Global styles for Telegram Mini App -->
    <style>
        /* Telegram Mini App global styles */
        html, body {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--tg-theme-bg-color, #ffffff);
            color: var(--tg-theme-text-color, #000000);
            overflow-x: hidden;
        }
        
        /* Telegram theme variables */
        :root {
            --tg-theme-bg-color: {{ env('TELEGRAM_THEME_BG_COLOR', '#ffffff') }};
            --tg-theme-text-color: {{ env('TELEGRAM_THEME_TEXT_COLOR', '#000000') }};
            --tg-theme-hint-color: {{ env('TELEGRAM_THEME_HINT_COLOR', '#999999') }};
            --tg-theme-link-color: {{ env('TELEGRAM_THEME_LINK_COLOR', '#2481cc') }};
            --tg-theme-button-color: {{ env('TELEGRAM_THEME_BUTTON_COLOR', '#2481cc') }};
            --tg-theme-button-text-color: {{ env('TELEGRAM_THEME_BUTTON_TEXT_COLOR', '#ffffff') }};
            --tg-theme-secondary-bg-color: {{ env('TELEGRAM_THEME_SECONDARY_BG_COLOR', '#f1f1f1') }};
        }
        
        /* Hide scrollbars but keep functionality */
        ::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }
        
        /* Loading spinner for initial app load */
        .app-loading {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--tg-theme-bg-color, #ffffff);
            z-index: 9999;
        }
        
        .app-loading__spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--tg-theme-secondary-bg-color, #f1f1f1);
            border-top: 3px solid var(--tg-theme-button-color, #2481cc);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    
    @stack('head')
</head>
<body class="@yield('body-class', '')">
    <!-- Loading screen -->
    <div id="app-loading" class="app-loading">
        <div class="app-loading__spinner"></div>
    </div>
    
    <!-- Main content -->
    <main id="main-content">
        @yield('content')
    </main>
    
    <!-- Telegram Web App initialization -->
    <script>
        // Initialize Telegram Web App
        if (window.Telegram && window.Telegram.WebApp) {
            const tg = window.Telegram.WebApp;
            
            // Configure Telegram Web App
            tg.ready();
            tg.expand();
            
            // Check version compatibility and set features accordingly
            const isVersionSupported = (requiredVersion) => {
                if (!tg.version) return false;
                return parseFloat(tg.version) >= parseFloat(requiredVersion);
            };
            
            // Set header color (supported from version 6.1+)
            if (isVersionSupported('6.1') && typeof tg.setHeaderColor === 'function') {
                tg.setHeaderColor('bg_color');
            }
            
            // Set background color (supported from version 6.1+)
            if (isVersionSupported('6.1') && typeof tg.setBackgroundColor === 'function') {
                tg.setBackgroundColor('#ffffff');
            }
            
            // Enable closing confirmation if needed
            @if(config('app.env') === 'production')
            if (typeof tg.enableClosingConfirmation === 'function') {
                tg.enableClosingConfirmation();
            }
            @endif
            
            // Apply Telegram theme
            if (tg.colorScheme) {
                document.documentElement.className = tg.colorScheme;
            }
            
            // Store Telegram data globally
            window.TelegramWebApp = tg;
            
            // Telegram Web App initialized
        } else {
            // Telegram Web App not available - running in browser mode
            window.TelegramWebApp = null;
        }
        
        // Hide loading screen when Vue app is ready
        window.hideAppLoading = function() {
            const loading = document.getElementById('app-loading');
            if (loading) {
                loading.style.opacity = '0';
                setTimeout(() => loading.remove(), 300);
            }
        };
    </script>
    
    <!-- Interface-specific scripts -->
    @stack('scripts')
    
    <!-- Development tools -->
    @if(config('app.env') === 'local')
    <script>
        // Development mode: Vue DevTools available
        
        // Hot reload notification (only available in development with Vite)
        if (typeof window !== 'undefined' && window.__vite_plugin_react_preamble_installed__) {
            // Hot reloading support available
        }
    </script>
    @endif
</body>
</html> 