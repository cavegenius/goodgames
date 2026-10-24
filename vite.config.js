import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    root: 'src',
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: { host: 'localhost' },
    },
    css: {
        preprocessorOptions: {
            scss: { quietDeps: true },
        },
    },
    plugins: [
        laravel({
            input: ['resources/sass/app.scss', 'resources/js/app.js'],
            publicDirectory: 'public',
            buildDirectory: 'build',
            hotFile: 'src/public/hot',
            refresh: true,
        }),
        vue(),
    ],
});
