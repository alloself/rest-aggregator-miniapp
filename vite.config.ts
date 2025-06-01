import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { fileURLToPath } from 'node:url';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Site (Публичный сайт)
                'resources/site/scss/app.scss',
                'resources/site/ts/main.ts',
                
                // Account (ЛК ресторанов)
                'resources/account/scss/app.scss', 
                'resources/account/ts/main.ts',
                
                // Admin (Админка)
                'resources/admin/scss/app.scss',
                'resources/admin/ts/main.ts',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources', import.meta.url)),
            '@shared': fileURLToPath(new URL('./resources/shared', import.meta.url)),
            '@types': fileURLToPath(new URL('./resources/shared/types', import.meta.url)),
            '@site': fileURLToPath(new URL('./resources/site', import.meta.url)),
            '@account': fileURLToPath(new URL('./resources/account', import.meta.url)),
            '@admin': fileURLToPath(new URL('./resources/admin', import.meta.url)),
        },
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
            port: 5173,
        },
        watch: {
            usePolling: true,
        },
    },
});
