> composer require inertiajs/inertia-laravel

> npm install inertia/inertia-vue --save

## Code splitting package - [required]
> npm install @babel/plugin-syntax-dynamic-import --save
.babelrc
```js
{
    "plugins": ["@babel/plugin-syntax-dynamic-import"]
}
```

webpack.config.js
```js
const path = require('path');
// setup @ - symbol directory
module.exports = {
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
        },
    },
};
```

webpack.mix.js
```js
const mix = require('laravel-mix');

// default
mix.js('resources/js/app.js', 'public/js')
    .vue({ version: 3 })
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'), 
        require('tailwindcss'), 
        require('autoprefixer')
    ])
    .webpackConfig(require('./webpack.config'));

// Dynamic
mix.js('resources/js/Frontend/StatusPage/index.js', 'public/js/statuspage.js')
    .vue({ version: 3 })
    .webpackConfig(require('./webpack.config'))
```