// vite.config.js
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { fileURLToPath, URL } from 'node:url';
import vue from '@vitejs/plugin-vue';

// https://vitejs.dev/config/
export default defineConfig(() => {
    return {
        plugins: [vue(), laravel(["public/layout/styles/preloading/preloading.css", "public/layout/styles/theme/theme-light/blue/theme.css", "src/main.js"])],
        resolve: {
            alias: {
                '@': fileURLToPath(new URL('./src', import.meta.url)),
                '@public': fileURLToPath(new URL('./public', import.meta.url))
            }
        }
    };
});
