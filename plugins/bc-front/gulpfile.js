/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * Gulpfile for BcFront
 * Require Gulp4
 */
const gulp = require("gulp");
const sass = require('gulp-sass')(require('sass'));
const plumber = require("gulp-plumber");
const uglify = require('gulp-uglify');
const paths = {
  srcCss :'./src/css/**/*.scss',
  distCss :'./webroot/css/',
  srcJs : './src/js/*.js',
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
