const {nextui} = require('@nextui-org/theme');
const flowbite = require("flowbite-react/tailwind");

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.jsx",
        "./resources/**/*.vue",
        // nextui().config.content(),
        "./node_modules/@nextui-org/theme/dist/components/(button|card|link|navbar|ripple|spinner).js",
        flowbite.content(),
    ],
    theme: {
        extend: {},
    },
    plugins: [nextui(), flowbite.plugin(),],
}

