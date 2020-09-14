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

// Disable OS notification when build
mix.disableNotifications();

mix.copy('node_modules/admin-lte/dist/img', 'public/img');
mix.js(
    [
        'node_modules/admin-lte/dist/js/adminlte.min.js',
        'node_modules/admin-lte/dist/js/demo.js',
        'node_modules/admin-lte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js'
    ],
    'public/js/vendor.js'
);

mix.styles(
    [
        'node_modules/admin-lte/dist/css/adminlte.css',
        'node_modules/admin-lte/plugins/overlayScrollbars/css/OverlayScrollbars.css'
    ],
    'public/css/vendor.css'
);


mix.js('resources/js/xgallery.js', 'public/js');

mix.sass('resources/sass/xgallery.scss', 'public/css')
    .options({
        postCss: [
            require('postcss-css-variables')()
        ],
        outputStyle: 'compress'
    });

if (mix.inProduction()) {
    mix.version();
}
