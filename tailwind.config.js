import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            gridTemplateColumns: {
                'main': 'repeat(8, 12vw)',
            },
            gridTemplateRows: {
                'main': 'repeat(3, 12vw)',
            },
        },
        fontFamily: {
            'display': ['Literata'],
        },
    },
    plugins: [],
};
