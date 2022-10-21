/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

const gulp = require("gulp");
const webpack = require('webpack');
const webpackStream = require('webpack-stream');
const webpackConfig = require("./webpack.config");
const JS_DEV_DIR = ['./src/js/'];
const JS_DIST_DIR = './webroot/js/';

gulp.task('bundle', () => {
	return webpackStream(webpackConfig, webpack)
	.pipe(gulp.dest(JS_DIST_DIR));
});

gulp.task('watch', function(){
    gulp.watch(JS_DEV_DIR + "**/*.js", gulp.task('bundle'));
});

gulp.task('default', gulp.task('watch'));
