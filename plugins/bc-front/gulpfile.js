/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Gulpfile for BcSample
 * Require Gulp4
 */
const gulp = require("gulp");
const sass = require('gulp-sass')(require('sass'));
const plumber = require("gulp-plumber");
const uglify = require('gulp-uglify');
const paths = {
  srcCss :'./webroot/src/css/**/*.scss',
  distCss :'./webroot/css/',
  srcJs : './webroot/src/js/*.js',
  destJs : './webroot/js/'
};

// Sass Compile
gulp.task("sass", function() {
  return gulp.src(paths.srcCss, {
      sourcemaps: true
  })
  .pipe(plumber({
      errorHandler: function(err) {
        console.log(err.messageFormatted);
        this.emit('end');
      }
    }))
  .pipe(sass())
  .pipe(gulp.dest(paths.distCss, {
	  sourcemaps: './maps'
  }));
});

// Minify Javascript
gulp.task('minjs', function(){
  gulp.src(paths.srcJs, {sourcemaps: true})
  .pipe(uglify())
  .pipe(gulp.dest(paths.destJs));
});

// File Watch
gulp.task('watch', function(done) {
  gulp.watch(paths.srcCss, gulp.task('sass'));
  gulp.watch(paths.srcJs, gulp.task('minjs'));
  done();
});

// Default
gulp.task('default', gulp.series('watch'));
