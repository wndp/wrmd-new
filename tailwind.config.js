import defaultTheme from 'tailwindcss/defaultTheme';
import colors from 'tailwindcss/colors';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import aspectRatio from '@tailwindcss/aspect-ratio';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    theme: {
        screens: {
            'sm': '640px',
            'md': '769px',
            'lg': '1024px',
            'xl': '1280px',
            '2xl': '1536px',
        },
        extend: {
            fontFamily: {
                sans: ['Open Sans', ...defaultTheme.fontFamily.sans],
            },
            screens: {
                'print': {'raw': 'print'},
            }
        },
        fontWeight: {
            normal: 400,
            medium: 600,
            bold: 700,
        },
        colors: {
            transparent: 'transparent',
            current: 'currentColor',
            black: colors.black,
            white: colors.white,
            gray: colors.neutral,
            red: {
                50: '#fef2f2',
                100: '#fee2e2',
                200: '#fef5f5',
                300: '#fcdede',
                400: '#f89595',
                500: '#f56565',
                600: '#f23535',
                700: '#e50f0f',
                800: '#b50c0c',
                900: '#860909'
            },
            orange: {
                50: '#fff7ed',
                100: '#ffedd5',
                200: '#fed7aa',
                300: '#fdba74',
                400: '#edce8b',
                500: '#e6bb5f',
                600: '#dfa833',
                700: '#c18d1e',
                800: '#956d17',
                900: '#694d10'
            },
            yellow: {
                50: '#fefce8',
                100: '#fef9c3',
                200: '#fef1c2',
                300: '#fde68f',
                400: '#fddb5d',
                500: '#fcd02b',
                600: '#f1bf03',
                700: '#be9703',
                800: '#8c6f02',
                900: '#5a4701'
            },
            green: {
                50: '#ecfdf5',
                100: '#d1fae5',
                200: '#edf4df',
                300: '#d9e7b9',
                400: '#c4da93',
                500: '#afcd6d',
                600: '#9ac047',
                700: '#7e9e36',
                800: '#5f7829',
                900: '#41521c'
            },
            blue: {
                50: '#eff6ff',
                100: '#dbeafe',
                200: '#e9f2f8',
                300: '#c2daec',
                400: '#9bc3e0',
                500: '#74abd4',
                600: '#4d93c8',
                700: '#357aad',
                800: '#295e86',
                900: '#1d435f'
            }
        }
    },
    variants: {
        extend: {
            opacity: ['disabled'],
        },
    },
    plugins: [forms, typography, aspectRatio],
};
