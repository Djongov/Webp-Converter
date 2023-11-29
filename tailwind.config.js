/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './**/*.php',
        './components/**/*.php'
    ],
    theme: {
        extend: {
            colors: {
                clifford: '#da373d',
            }
        }
    },
    plugins: [],
    darkMode: 'class',
}