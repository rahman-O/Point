const { nextui } = require('@nextui-org/theme');
const flowbite = require('flowbite-react/tailwind');

/** @type {import('tailwindcss').Config} */
export default {
	content: [
		'./resources/**/*.blade.php',
		'./resources/**/*.js',
		'./resources/**/*.jsx',
		'./resources/**/*.vue',
		// nextui().config.content(),
		'./node_modules/@nextui-org/theme/dist/components/(button|card|link|navbar|ripple|spinner).js',
		flowbite.content(),
	],
	theme: {
		extend: {
			colors: {
				'point-teal': '#487879',
				'point-rose': '#bc4d58',
				// Point conference (point.zastone.ba) brand palette
				'point-purple': '#7C2C8B',
				'point-orange': '#F9A036',
				'point-crimson': '#D81C5C',
			},
		},
	},
	plugins: [nextui(), flowbite.plugin()],
};
