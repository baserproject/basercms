const gulp = require("gulp");
const webpack = require('webpack');
const webpackStream = require('webpack-stream');
const webpackConfig = require("./webpack.config");
const targetJs = ['./webroot/js/src/**/*'];

gulp.task('bundle', function(){
	return webpackStream(webpackConfig, webpack).pipe(gulp.dest('./webroot/js'));
});
gulp.task('series',
    gulp.series(
        'bundle'
    )
);
gulp.task('watch', function(){
    gulp.watch(targetJs).on('change', gulp.task('series'));
});
gulp.task('default', gulp.task('watch'));
