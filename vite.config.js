import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from "tailwindcss";




export default defineConfig({
    css: {
        postcss: {
            plugins: [tailwindcss],
        },


    },
    plugins: [

        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.jsx',
                    //'resources/js/Pages/'
            ],
            refresh: true,
        }),
        react(),
    ],
});
