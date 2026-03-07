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
mix.options({
    terser: {
        terserOptions: {
            compress: {
                // Strip out console log cmds
                drop_console: true,
            }
        }
    }
});

mix.js('resources/js/admin/app.js', 'public/js/admin')
   .js('resources/js/store/store_app.js', 'public/js/store')
   .js('resources/js/frontend/social-share-app.js', 'public/js/frontend')
   .sass('resources/sass/admin/admin-app.scss', 'public/css/admin')
   .sass('resources/sass/store/store-app.scss', 'public/css/store');

// Version files for production build
if (mix.inProduction()) {
      mix.version();
}