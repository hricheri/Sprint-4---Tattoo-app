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
                    50: '#f6f4fa',
                    100: '#ebe6f4',
                    200: '#d7cfe8',
                    300: '#c1b5d9',
                    400: '#b6a9d1',
                    500: '#a99bc4',
                    600: '#8a76b2',
                    700: '#6c549c',
                    800: '#523e79',
                    900: '#3b2c59',
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