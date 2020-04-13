const mix = require("laravel-mix");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js("resources/js/app.js", "public/js").postCss(
    "resources/css/app.css",
    "public/css"
);

mix.copy(
    "node_modules/datatables.net-buttons/js/buttons.html5.min.js",
    "public/vendor/datatables.net-buttons/js/buttons.html5.min.js"
);

mix.copy(
    "node_modules/jszip/dist/jszip.min.js",
    "public/vendor/jszip/dist/jszip.min.js"
);

mix.copy("resources/img", "public/img");
