import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/kanban.css',
                'resources/js/kanban.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: 'localhost',
        port: 5173,
        strictPort: true,
        open: false,
        hmr: {
            overlay: true,
        },
    },
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});
