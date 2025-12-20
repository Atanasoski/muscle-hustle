import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/mkocansey/bladewind/src/resources/views/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Default Muscle Hustle brand colors
                primary: {
                    DEFAULT: '#ff6b35',
                    50: '#fff5f2',
                    100: '#ffe8e0',
                    200: '#ffd1c2',
                    300: '#ffb195',
                    400: '#ff8c61',
                    500: '#ff6b35',
                    600: '#f04e1b',
                    700: '#d13b0f',
                    800: '#a82e0c',
                    900: '#8a2710',
                },
                secondary: {
                    DEFAULT: '#4ecdc4',
                    50: '#f0fdfb',
                    100: '#ccfbf7',
                    200: '#99f6f0',
                    300: '#5de9e1',
                    400: '#4ecdc4',
                    500: '#23b5ac',
                    600: '#179189',
                    700: '#177370',
                    800: '#175c5a',
                    900: '#184c4b',
                },
            },
        },
    },

    plugins: [forms],
};
