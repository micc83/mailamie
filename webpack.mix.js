let mix = require('laravel-mix');
require('laravel-mix-svg-vue');

mix
    .js('assets/app.js', 'public/')
    .styles('assets/app.css', 'public/app.css')
    .svgVue({svgPath:'assets/icons'});
