const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/extend.js', 'public/assets/js')
    .postCss('resources/css/extend.css', 'public/assets/css', [
        require('tailwindcss'),
    ])
    .postCss('resources/css/red-theme.css', 'public/assets/css', [
        require('tailwindcss'),
    ])
    .copy('resources/img/svg/*.svg', 'public/img/svg')
    .copy('resources/img/png/*.png', 'public/img/png')
    .copy('vendor/filament/filament/dist/app.js', 'public/assets')
    .copy('vendor/filament/filament/dist/app.js.map', 'public/assets')
    .copy('vendor/livewire/livewire/dist/livewire.js', 'public/livewire')
    .copy('vendor/livewire/livewire/dist/livewire.js.map', 'public/livewire')
    .version()
;
