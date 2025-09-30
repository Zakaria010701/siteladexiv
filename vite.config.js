import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/app.css',
                'resources/js/cms/cms.js',
                'resources/css/cms/cms.css',
                'resources/css/filament/admin/theme.css',
                'resources/css/filament/cms/theme.css',
                'resources/css/filament/crm/theme.css',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
