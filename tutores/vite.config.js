import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { local } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Inter autoalojada: es la tipografía del diseño y evita depender de
            // fonts.googleapis.com en tiempo de ejecución.
            //
            // Se sirve solo woff2 a propósito. Los proveedores remotos emiten
            // además una regla @font-face con woff que, al ir después, gana el
            // desempate del CSS: el navegador acababa descargando los woff2
            // precargados para descartarlos y quedarse con los woff, más pesados.
            // Las variantes van explícitas: la forma abreviada con glob colapsa
            // los cinco archivos en una sola regla de peso 400 y el navegador
            // acaba sintetizando las negritas.
            fonts: [
                local('Inter', {
                    variants: [
                        { src: 'resources/fonts/inter-400-normal.woff2', weight: 400 },
                        { src: 'resources/fonts/inter-500-normal.woff2', weight: 500 },
                        { src: 'resources/fonts/inter-600-normal.woff2', weight: 600 },
                        { src: 'resources/fonts/inter-700-normal.woff2', weight: 700 },
                        { src: 'resources/fonts/inter-800-normal.woff2', weight: 800 },
                    ],
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
