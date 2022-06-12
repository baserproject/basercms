const gulp = require("gulp");
const plumber = require('gulp-plumber');
const sass = require('gulp-sass')(require('sass'));
const CSS_DEV_DIR = './__assets/css/';
const CSS_DIST_DIR = './css/admin/';

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

gulp.task('watch', function(){
    gulp.watch([CSS_DEV_DIR + "**/*.scss"], gulp.task('css'));
});

gulp.task('default', gulp.task('watch'));
