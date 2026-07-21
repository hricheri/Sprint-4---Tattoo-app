import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
                heading: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            
            colors: {
                lavender: {
                    50: '#f5f3fc',
                    100: '#ebe6f9',
                    200: '#d5cbf2',
                    300: '#b8a6e8',
                    400: '#9c7fd9',
                    500: '#8560c9',
                    600: '#7048b0',
                    700: '#5c3a91',
                    800: '#4c3176',
                    900: '#402a61',
                },
                lima: {
                    50: '#f7fee0',
                    100: '#eefcc2',
                    200: '#dcf88a',
                    300: '#c4f135',
                    400: '#b0dc1f',
                    500: '#93bd14',
                    600: '#729210',
                    700: '#566f11',
                    800: '#455714',
                    900: '#3b4a16',
                },
            },
        },
    },

    plugins: [forms],
};