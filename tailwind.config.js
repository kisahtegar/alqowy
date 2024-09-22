/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],
    theme: {
        extend: {
            fontFamily: {
                poppins: "Poppins",
            },
            keyframes: {
                slideToL: {
                    "0%": { transform: "translateX(0%)" },
                    "100%": { transform: "translateX(-100%)" },
                },
                slideToR: {
                    "0%": { transform: "translateX(-100%)" },
                    "100%": { transform: "translateX(0%)" },
                },
            },
        },
    },
    plugins: [],
};
