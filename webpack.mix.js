const mix = require('laravel-mix');

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

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');

mix.copy('resources/css/espire.css', 'public/css/espire.css');

mix.js('resources/js/espire.js', 'public/js/espire.js');

mix.copy('node_modules/perfect-scrollbar/dist/js/min/perfect-scrollbar.jquery.min.js',
    'public/vendor/perfect-scrollbar/dist/js/min/perfect-scrollbar.jquery.min.js');

mix.copy('node_modules/perfect-scrollbar/dist/css/perfect-scrollbar.min.css',
    'public/vendor/perfect-scrollbar/dist/css/perfect-scrollbar.min.css');

// mix.copy(
//     'node_modules/datatables.net-buttons',
//     'public/vendor/datatables.net-buttons'
// );

mix.copy(
    'node_modules/datatables.net-buttons/js/buttons.html5.min.js',
    'public/vendor/datatables.net-buttons/js/buttons.html5.min.js'
);

mix.copy(
    'node_modules/jszip/dist/jszip.min.js',
    'public/vendor/jszip/dist/jszip.min.js'
);
