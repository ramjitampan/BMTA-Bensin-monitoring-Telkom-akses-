import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/Tema/layout.css',
                'resources/Tema/beranda/beranda.css',
                'resources/Tema/pegawai/pegawai.css',
                'resources/Tema/kendaraan/kendaraan.css',
                'resources/Tema/kendaraan/edit.css',
                'resources/Tema/perjalanan/perjalanan.css',
                'resources/Tema/perjalanan/create.css',
                'resources/Tema/perjalanan/edit.css',
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
