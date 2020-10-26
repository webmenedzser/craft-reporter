let mix = require('laravel-mix');
const glob = require('glob-all');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application, as well as bundling up your JS files.
 |
 */

const BROWSER_SYNC_PROXY = 'localhost';
// const EXTRACT_VENDORS = ['vue'];

mix.setPublicPath('src/assetbundles/');

/*
 |--------------------------------------------------------------------------
 | JavaScript
 |--------------------------------------------------------------------------
 |
 | There is a lot going on here: ES2017+ modules compilation, build and
 | compile .vue components, hot module replacement and tree-shaking.
 | You can even bundle multiple files into one or have multiple
 | entry/output points.
 |
 | Mix Docs: https://laravel-mix.com/docs/2.1/mixjs
 |
 */

mix.js('src/assetbundles/utilities/src/js/app.js', 'utilities/dist/js/');
