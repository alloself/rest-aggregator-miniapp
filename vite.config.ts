import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { fileURLToPath } from "node:url";
import vue from "@vitejs/plugin-vue";
import Components from "unplugin-vue-components/vite";
import { PrimeVueResolver } from "@primevue/auto-import-resolver";
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/site/css/app.css",
                "resources/site/ts/main.ts",

                "resources/account/css/app.css",
                "resources/account/ts/main.ts",

                "resources/admin/css/app.css",
                "resources/admin/ts/main.ts",
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        Components({
            resolvers: [PrimeVueResolver()],
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            "@": fileURLToPath(new URL("./resources", import.meta.url)),
            "@shared": fileURLToPath(
                new URL("./resources/shared", import.meta.url),
            ),
            "@types": fileURLToPath(
                new URL("./resources/shared/types", import.meta.url),
            ),
            "@site": fileURLToPath(
                new URL("./resources/site", import.meta.url),
            ),
            "@account": fileURLToPath(
                new URL("./resources/account", import.meta.url),
            ),
            "@admin": fileURLToPath(
                new URL("./resources/admin", import.meta.url),
            ),
        },
    },
    server: {
        host: "0.0.0.0",
        port: 5173,
        hmr: {
            host: "localhost",
            port: 5173,
        },
        watch: {
            usePolling: true,
        },
    },
});
