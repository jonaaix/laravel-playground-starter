import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";
import vue from '@vitejs/plugin-vue';
import { fileURLToPath, URL } from 'node:url';
import islands from './vendor/aaix/laravel-islands/vite.js';
import islandsDatagrid from './vendor/aaix/laravel-islands-datagrid/vite.js';
import islandsSearch from './vendor/aaix/laravel-islands-search/vite.js';
import islandsGallery from './vendor/aaix/laravel-islands-gallery/vite.js';

export default defineConfig(() => {
    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/filament/admin/theme.css'],
                refresh: true,
            }),
            tailwindcss(),
            vue(),
            islands({ theme: { output: false } }),
            islandsDatagrid(),
            islandsSearch(),
            islandsGallery(),
        ],
        resolve: {
            alias: {
                '@shared': fileURLToPath(
                    new URL('./app/Islands/@Shared', import.meta.url),
                ),
            },
        },
        server: {
            host: '0.0.0.0',
            port: 5178,
            origin: 'http://localhost:5178',
            cors: {
                origin: /^http:\/\/(localhost|127\.0\.0\.1)(:\d+)?$/,
            },
            hmr: {
                host: 'localhost',
            },
            watch: {
                ignored: ['**/vendor/**', '**/storage/**'],
            },
        },
    };
});
