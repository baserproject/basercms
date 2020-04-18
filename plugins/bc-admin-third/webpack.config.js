const path = require("path");
const glob = require("glob");
var entries = {};
glob.sync("./webroot/js/src/**/*.js").map(function(file){
    entries[file.replace('./webroot/js/src/', '').split('.').shift()] = file;
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
			name: 'vendor',
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
            }
        ]
    }
};
