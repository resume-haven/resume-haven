/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                primary: "#2D6CDF",
                "primary-dark": "#1e40af",

                "neutral-dark": "#2B2B2B",
                "neutral-light": "#f3f4f6",

                "text-light": "#111827",
                "text-dark": "#e5e7eb",

                match: "#16a34a",
                gap: "#dc2626",
            },
        },
    },
    plugins: [],
}


