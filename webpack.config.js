const path = require('path');
const CopyPlugin = require('copy-webpack-plugin');

const NODE_MODULES_PATH = path.resolve(__dirname, 'node_modules');
const LIB_PATH = path.resolve(__dirname, 'public/assets/libraries');

let copyingPaths = [
    {from: NODE_MODULES_PATH + '/bootstrap/dist/js/bootstrap.min.js', to: LIB_PATH + '/bootstrap/js/bootstrap.min.js'},
    {
        from: NODE_MODULES_PATH + '/bootstrap/dist/css/bootstrap.min.css',
        to: LIB_PATH + '/bootstrap/css/bootstrap.min.css'
    },
    {
        from: NODE_MODULES_PATH + '/bootstrap/dist/css/bootstrap-theme.min.css',
        to: LIB_PATH + '/bootstrap/css/bootstrap-theme.min.css'
    },
    {from: NODE_MODULES_PATH + '/bootstrap/dist/fonts', to: LIB_PATH + '/bootstrap/fonts'},

    {from: NODE_MODULES_PATH + '/jquery/dist/jquery.min.js', to: LIB_PATH + '/jquery/jquery.min.js'},
    {from: NODE_MODULES_PATH + '/jquery-ui/dist/jquery-ui.min.js', to: LIB_PATH + '/jquery-ui/jquery-ui.min.js'},
    {
        from: NODE_MODULES_PATH + '/jquery-ui/dist/themes/base/jquery-ui.min.css',
        to: LIB_PATH + '/jquery-ui/themes/base/jquery-ui.min.css'
    },
    {
        from: NODE_MODULES_PATH + '/jquery-validation/dist/jquery.validate.min.js',
        to: LIB_PATH + '/jquery-validation/jquery.validate.min.js',
    },
    {
        from: NODE_MODULES_PATH + '/@fancyapps/fancybox/dist/jquery.fancybox.min.js',
        to: LIB_PATH + '/fancybox/jquery.fancybox.min.js',
    },
    {
        from: NODE_MODULES_PATH + '/@fancyapps/fancybox/dist/jquery.fancybox.min.css',
        to: LIB_PATH + '/fancybox/jquery.fancybox.min.css',
    },
    {
        from: NODE_MODULES_PATH + '/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
        to: LIB_PATH + '/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css'
    },
    {
        from: NODE_MODULES_PATH + '/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
        to: LIB_PATH + '/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'
    },
    {from: NODE_MODULES_PATH + '/moment/min/moment.min.js', to: LIB_PATH + '/moment/moment.min.js'},
    {from: NODE_MODULES_PATH + '/moment/locale/ru.js', to: LIB_PATH + '/moment/moment.ru.min.js'},
    {
        from: NODE_MODULES_PATH + '/font-awesome/css/font-awesome.min.css',
        to: LIB_PATH + '/font-awesome/css/font-awesome.min.css'
    },
    {from: NODE_MODULES_PATH + '/font-awesome/fonts', to: LIB_PATH + '/font-awesome/fonts'},

    {from: NODE_MODULES_PATH + '/ace-builds/src-min/ace.js', to: LIB_PATH + '/ace/ace.js'},
    {from: NODE_MODULES_PATH + '/ace-builds/src-min/mode-html.js', to: LIB_PATH + '/ace/mode-html.js'},
    {from: NODE_MODULES_PATH + '/ace-builds/src-min/worker-html.js', to: LIB_PATH + '/ace/worker-html.js'},
    {from: NODE_MODULES_PATH + '/ace-builds/src-min/mode-sql.js', to: LIB_PATH + '/ace/node-sql.js'},
    {from: NODE_MODULES_PATH + '/ace-builds/src-min/mode-php.js', to: LIB_PATH + '/ace/node-php.js'},
    {from: NODE_MODULES_PATH + '/ace-builds/src-min/worker-php.js', to: LIB_PATH + '/ace/worker-php.js'},
    {from: NODE_MODULES_PATH + '/ace-builds/src-min/worker-xml.js', to: LIB_PATH + '/ace/worker-xml.js'},
    {from: NODE_MODULES_PATH + '/ace-builds/src-min/mode-text.js', to: LIB_PATH + '/ace/mode-text.js'},
    {from: NODE_MODULES_PATH + '/ace-builds/src-min/mode-javascript.js', to: LIB_PATH + '/ace/mode-javascript.js'},
    {from: NODE_MODULES_PATH + '/ace-builds/src-min/worker-javascript.js', to: LIB_PATH + '/ace/worker-javascript.js'},
    {from: NODE_MODULES_PATH + '/ace-builds/src-min/mode-json.js', to: LIB_PATH + '/ace/mode-json.js'},
    {from: NODE_MODULES_PATH + '/ace-builds/src-min/worker-json.js', to: LIB_PATH + '/ace/worker-json.js'},
    {from: NODE_MODULES_PATH + '/ace-builds/src-min/ext-searchbox.js', to: LIB_PATH + '/ace/ext-searchbox.js'},
];

let pluginsArr = [];

pluginsArr.push(new CopyPlugin({patterns: copyingPaths}));

module.exports = {
    mode: 'production',
    resolve: {},
    entry: {},
    output: {},
    module: {
        rules: []
    },
    plugins: pluginsArr,
    optimization: {
        minimize: false
    },
    stats: {
        entrypoints: false,
        children: false
    }
};

