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

mix.js('resources/js/admin/app.js', 'public/js/admin')
    .react('resources/js/app.js', 'public/js/app.js')
    .sass('resources/sass/truck/client/styles.scss', 'public/css/client')
    .sass('resources/sass/truck/admin/styles.scss', 'public/css/admin')
    .sass('resources/sass/styles.scss', 'public/css');
