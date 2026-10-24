import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    root: 'src',
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
            refresh: true,
        }),
        vue(),
    ],
});
