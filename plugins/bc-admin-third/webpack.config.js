/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */


const path = require("path");
const glob = require("glob");
var entries = {};
const webpack = require('webpack');

glob.sync("./webroot/js/src/**/*.js").map(function(file){
    if(!file.replace('./webroot/js/src/admin/', '').match(/^_/)) {
        entries[file.replace('./webroot/js/src/', '').split('.').shift()] = file;
    }
});

module.exports = {
    mode: 'production',
	entry: entries,
    devtool: 'source-map',
    output: {
    	path: path.resolve(__dirname, './webroot/js'),
		filename: "[name].bundle.js"
    },
    optimization: {
        splitChunks: {
			name: 'admin/vendor',
			chunks: 'initial',
        }
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
