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
const plumber = require('gulp-plumber');
const sass = require('gulp-sass')(require('sass'));
const postcss = require("gulp-postcss");
const cssImport = require("postcss-import");
const JS_DEV_DIR = './src/**/*.js';
const JS_DIST_DIR = './webroot/';
const CSS_DEV_DIR = ['./src/**/*.scss', '!./src/css/vendor/**/*.scss'];
const CSS_DIST_DIR = './webroot/';

gulp.task('css', () => {
	const plugins = [
		cssImport({
			path: [ '../../node_modules' ]
		})
	];
	return gulp
	.src(CSS_DEV_DIR, {
	    sourcemaps: true
	})
	.pipe(plumber({
		errorHandler: function (err) {
		    console.log(err.message);
			this.emit('end');
		},
	}))
	.pipe(sass())
	.pipe(postcss(plugins))
	.pipe(gulp.dest(CSS_DIST_DIR, {
	    sourcemaps: true
	}));
});

gulp.task('bundle', () => {
	return webpackStream(webpackConfig, webpack)
	.pipe(gulp.dest(JS_DIST_DIR));
});

gulp.task('watch', function(){
    gulp.watch(CSS_DEV_DIR, gulp.task('css'));
    gulp.watch(JS_DEV_DIR, gulp.task('bundle'));
});

gulp.task('default', gulp.task('watch'));
