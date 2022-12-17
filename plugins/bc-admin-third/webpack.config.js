/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */


const glob = require("glob");
const TerserPlugin = require('terser-webpack-plugin');
let entries = {};
webpack = require('webpack');

glob.sync("./src/**/*.js").map(function(file){
    if(!file.search('./src/js/admin/', '')) {
        if(!file.replace('./src/js/admin/', '').match(/^_/)) {
            entries[file.replace('./src/', '').split('.').shift()] = file;
        }
    } else if(file.match(/\.\/src\/.+\/js\/admin\//, '')) {
        if(!file.replace(/\.\/src\/.+\/js\/admin\//, '').match(/^_/)) {
            entries[file.replace(/\.\/src\//, '').split('.').shift()] = file;
        }
    }
});

module.exports = {
    mode: 'production',
	entry: entries,
    devtool: 'source-map',
    output: {
		filename: "[name].bundle.js"
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
    module: {
        rules: [
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
