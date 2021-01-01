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




mix.styles([
    'resources/css/bootstrap.css',
    'resources/css/bootstrap-grid.css',
    'resources/css/bootstrap-reboot.css',
    'resources/css/fonts.css',
    'resources/css/base.css'
], 'public/css/styles.css')

mix.scripts([
    'resources/js/jquery-3.5.1.js',
    'resources/js/bootstrap.js',
], 'public/js/scripts.js');
