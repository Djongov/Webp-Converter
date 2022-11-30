/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./src/**/*.php", './components/**/*.php'],
    theme: {
            extend: {
                colors: {
                    clifford: '#da373d',
                }
            }
        },
        darkMode: 'class',
}