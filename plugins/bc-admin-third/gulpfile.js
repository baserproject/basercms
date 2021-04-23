const gulp = require("gulp");
const webpack = require('webpack');
const webpackStream = require('webpack-stream');
const webpackConfig = require("./webpack.config");
const plumber = require('gulp-plumber');
const sass = require('gulp-sass');
const JS_DEV_DIR = ['./webroot/js/src/**/*'];
const JS_DIST_DIR = './webroot/js';
const CSS_DEV_DIR = './__assets/css/';
const CSS_DIST_DIR = './webroot/css/admin/';

gulp.task('css', () => {
	return gulp
	.src(`${CSS_DEV_DIR}*`, {
	    sourcemaps: true
	})
	.pipe(plumber({
		errorHandler: function (err) {
		    console.log(err.message);
			this.emit('end');
		},
	}))
	.pipe(sass())
	.pipe(gulp.dest(CSS_DIST_DIR, {
	    sourcemaps: true
	}));
});

gulp.task('bundle', () => {
	return webpackStream(webpackConfig, webpack)
	.pipe(gulp.dest(JS_DIST_DIR));
});

gulp.task('watch', function(){
    gulp.watch([CSS_DEV_DIR + "**/*.scss"], gulp.task('css'));
    gulp.watch(JS_DEV_DIR, gulp.task('bundle'));
});

gulp.task('default', gulp.task('watch'));
