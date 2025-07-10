import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import fs from 'fs';

export default defineConfig({
    server: {
        https: {
            key: fs.readFileSync('/Users/denispopov/Sites/favlesson/certs/favlesson-key.pem'),
            cert: fs.readFileSync('/Users/denispopov/Sites/favlesson/certs/favlesson.pem'),
        },
        port: 5173,
        cors: true,
        host: 'favlesson.loc',
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
