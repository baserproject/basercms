const gulp = require('gulp');
const plumber = require('gulp-plumber');
const runSequence = require('run-sequence');
const browserSync = require('browser-sync');
const sass = require('gulp-sass');
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');

const sceg = require('gulp-sceg');

const BROWSER_SYNC_PROXY = 'localhost:8888';

const CSS_DEV_DIR = 'css/';
const CSS_MAIN_SCSS_FILENAME = 'style.scss';
const CSS_DIST_DIR = '../css/admin/';

gulp.task('css', () => {
	return gulp
	.src(`${CSS_DEV_DIR}${CSS_MAIN_SCSS_FILENAME}`)
	.pipe(plumber({
		errorHandler: function (err) {
			console.log(err.messageFormatted);
			this.emit('end');
		},
	}))
	.pipe(sass())
	.pipe(postcss([
		autoprefixer({browsers: [
			'last 1 version',
		]}),
	]))
	.pipe(gulp.dest(CSS_DIST_DIR))
	.pipe(browserSync.stream());
});

gulp.task('guide', () => {
	return gulp
	.src(`./guide/elements/*`)
	.pipe(sceg({
		layout: `./guide/index.hbs`,
		filename: `guide.html`,
	}))
	.pipe(gulp.dest(`../`))
});

gulp.task('watch', () => {
	browserSync.init({
		proxy: BROWSER_SYNC_PROXY,
	});
	gulp.watch([`${CSS_DEV_DIR}**/*.scss`], ['css']);
});

gulp.task('build', ['css']);

gulp.task('default', ['build']);
