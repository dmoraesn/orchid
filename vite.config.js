import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import path from 'path';

export default defineConfig({
    plugins: [
        // 🔹 Plugin principal do Laravel
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',

                // Kanban personalizado
                'resources/css/kanban.css',
                'resources/js/kanban.js',
            ],
            refresh: ['resources/views/**'], // Hot reload também nas views Blade
        }),

        // 🔹 TailwindCSS (diretamente integrado com PostCSS)
        tailwindcss(),
    ],

    // 🔹 Servidor de desenvolvimento
    server: {
        host: '127.0.0.1', // Evita problemas em Windows com "localhost"
        port: 5173,
        strictPort: true,
        open: false,
        hmr: {
            overlay: true, // Mostra erros de build direto no navegador
        },
    },

    // 🔹 Otimização e compatibilidade
    build: {
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            output: {
                chunkFileNames: 'js/[name]-[hash].js',
                entryFileNames: 'js/[name]-[hash].js',
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name && assetInfo.name.endsWith('.css')) {
                        return 'css/[name]-[hash][extname]';
                    }
                    return 'assets/[name]-[hash][extname]';
                },
            },
        },
    },

    // 🔹 Alias e resolução de paths
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '~': path.resolve(__dirname, 'resources'),
        },
    },

    // 🔹 Log mais limpo
    logLevel: 'info',
});
