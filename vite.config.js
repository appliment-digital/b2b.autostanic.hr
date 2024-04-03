// vite.config.js
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { fileURLToPath, URL } from 'node:url';
import vue from '@vitejs/plugin-vue';

// https://vitejs.dev/config/
export default defineConfig(() => {
    return {
        plugins: [
            vue({
                template: {
                    transformAssetUrls: {
                        includeAbsolute: false,
                    },
                },
            }),
            laravel({
                input: ["public/layout/styles/preloading/preloading.css", "public/layout/styles/theme/theme-light/blue/theme.css", "public/layout/styles/theme/theme-light/teal/theme.css", "src/main.js"],
                refresh: true
            })
        ],
        resolve: {
            alias: {
                '@': fileURLToPath(new URL('./src', import.meta.url)),
                '@public': fileURLToPath(new URL('./public', import.meta.url))
            }
        }
    };
});
