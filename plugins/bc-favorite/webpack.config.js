
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const glob = require("glob");
var entries = {};
const path = require('path');

glob.sync("./webroot/js/src/**/*.js").map(function(file){
    if(!file.replace('./webroot/js/src/admin/', '').match(/^_/)) {
        entries[file.replace('./webroot/js/src/', '').split('.').shift()] = file;
    }
});

module.exports = (env, argv) => {
    console.log("aaa" + __dirname);
    return {
        // entry: ['@babel/polyfill', './webroot/js/src/admin/favorites/main.js'],
        entry: entries,
        output: {
            filename: '[name].bundle.js',
            path: path.join(__dirname, 'webroot/js')
        },
        plugins: [
            new VueLoaderPlugin()
        ],
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    use: [
                        {
                            loader: 'babel-loader',
                            options: {
                                presets: [['@babel/preset-env', {modules: false}]]
                            }
                        }
                    ]
                },
                {
                    test: /\.vue$/,
                    exclude: /node_modules/,
                    loader: ['vue-loader']
                }
            ]
        },
        devtool: 'source-map'
    };
};
