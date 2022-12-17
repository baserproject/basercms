/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

const VueLoaderPlugin = require('vue-loader/lib/plugin');
const glob = require("glob");
const TerserPlugin = require('terser-webpack-plugin');
const path = require('path');
const webpack = require('webpack');
let entries = {};

glob.sync("./src/**/*.js").map(function (file) {
    if (!file.search('./src/js/admin/', '')) {
        if (!file.replace('./src/js/admin/', '').match(/^_/)) {
            entries[file.replace('./src/', '').split('.').shift()] = file;
        }
    } else if (file.match(/\.\/src\/.+\/js\/admin\//, '')) {
        if (!file.replace(/\.\/src\/.+\/js\/admin\//, '').match(/^_/)) {
            entries[file.replace(/\.\/src\//, '').split('.').shift()] = file;
        }
    }
});

module.exports = {
    mode: 'production',
    entry: entries,
    devtool: 'source-map',
    output: {
        filename: "[name].bundle.js",
        path: path.join(__dirname, 'webroot/js')
    },
    resolve: {
        alias: {
            process: "process/browser"
        }
    },
    optimization: {
        splitChunks: {
            name: 'js/admin/vendor',
            chunks: 'initial',
        },
        minimizer: [new TerserPlugin({
            extractComments: false,
        })],
    },
    plugins: [
        new VueLoaderPlugin(),
        // webpack5 に移行時の問題に必要となった
        // vuelidate を読み込む際に、process が見つからないというエラーがあり、BcFavoriteが動かなくなったため
        // npm で、process をインストールして利用する
        new webpack.ProvidePlugin({
            process: 'process/browser',
        }),
    ],
    module: {
        rules: [
            {
                test: /\.vue$/,
                exclude: /node_modules/,
                loader: 'vue-loader'
            },
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env']
                    }
                }
            },
            {
                test: /\.(sc|c|sa)ss$/,
                use: [
                    'style-loader',
                    'css-loader'
                ]
            }
        ]
    }
};
