import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                'cc-red': '#D42B2B',
                'cc-red-dark': '#B02020',
                'cc-red-light': '#FAE8E8',
                'cc-blue': '#1A3A6B',
                'cc-blue-mid': '#2B5FAA',
                'cc-blue-light': '#E8EFF8',
                'cc-black': '#1A1A1A',
                'cc-gray': '#444444',
                'cc-gray-mid': '#767676',
                'cc-gray-bg': '#F5F5F5',
                'cc-border': '#E0E0E0',
                'cc-yellow': '#FFF3CD',
                'cc-yellow-txt': '#856404',
                'cc-green': '#198754',
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            borderRadius: {
                cc: '4px',
                'cc-card': '8px',
            },
            boxShadow: {
                'cc-card': '0 2px 8px rgba(0,0,0,0.08)',
                'cc-card-hover': '0 4px 16px rgba(0,0,0,0.12)',
            },
        },
    },

    plugins: [forms],
};
