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
                sans: ['Outfit', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                cosmos: {
                    900: '#0b0c10',
                    800: '#14161c',
                    700: '#1e222d',
                },
                shopee: {
                    DEFAULT: '#ee4d2d',
                    light: '#ff7337',
                    dark: '#d03b1d',
                }
            }
        },
    },

    plugins: [forms],
};
