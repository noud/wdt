const Encore = require('@symfony/webpack-encore');
const CopyWebpackPlugin = require('copy-webpack-plugin');
require('jquery');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
     */
    .addEntry('app', './assets/js/app.js')
    .addEntry('password', './assets/js/password.js')
    .addEntry('dropzone', './assets/js/dropzone.js')
    .addEntry('add_ticket', './assets/js/add_ticket.js')
    .addEntry('ticket_status', './assets/js/ticket_status.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /* login
       ========================================================================== */
    // .addEntry('login', './assets/js/login.js')

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning()
    .autoProvidejQuery()
    .addPlugin(new CopyWebpackPlugin())
    .autoProvideVariables({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
    })
;

const config = Encore.getWebpackConfig();

config.watchOptions = {
    poll: true,
};

module.exports = config;
