var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    .cleanupOutputBeforeBuild()

    .enableSourceMaps(!Encore.isProduction())

    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())
    // uncomment to define the assets of the project
    .addEntry('index', './assets/css/index.css')
    .addEntry('trick', './assets/css/trick.css')
    .addEntry('account', './assets/css/account.css')
    .addEntry('addTrickForm', './assets/css/addTrickForm.css')
    .addEntry('layout', './assets/css/layout.css')
    .addEntry('form', './assets/js/form.js')
    .addEntry('modify_form', './assets/js/modify_form.js')
    .addEntry('view','./assets/js/view.js')
    .addEntry('login', './assets/css/login.css')
    .addEntry('ajax','./assets/js/ajaxLp.js')

    // uncomment if you use Sass/SCSS files
    // .enableSassLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
