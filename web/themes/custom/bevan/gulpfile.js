'use strict';

var gulp = require('gulp'),
	sass = require('gulp-sass');

const sassConfig = {
	inputDirectory: './sass',
	inputFile: './sass/styles.scss',
	outputDirectory: './css',
	options: {
		outputStyle: 'compressed'
	}
}

const govukLibraryDirectory = './node_modules/govuk-frontend/';

const assetsConfig = {
	inputDirectory: govukLibraryDirectory + 'assets/*/*',
	outputDirectory: './assets'
}

const jsConfig = {
	inputFile: govukLibraryDirectory + 'all.js',
	outputDirectory: './js'
}

gulp.task('build-sass', function() {
	return gulp
		.src(sassConfig.inputFile)
		.pipe(sass(sassConfig.options).on('error', sass.logError))
		.pipe(gulp.dest(sassConfig.outputDirectory));
});

gulp.task('copy-assets', function() {
	return gulp
		.src(assetsConfig.inputDirectory)
		.pipe(gulp.dest(assetsConfig.outputDirectory))
});

gulp.task('copy-js', function() {
	return gulp
		.src(jsConfig.inputFile)
		.pipe(gulp.dest(jsConfig.outputDirectory))
});

gulp.task('build', gulp.series('build-sass', 'copy-assets', 'copy-js'));

gulp.task('watch', function() {
	gulp.watch(sassConfig.inputDirectory + '/**/*.scss', gulp.series('build-sass'));
  gulp.watch(assetsConfig.inputDirectory, gulp.series('copy-assets'));
  gulp.watch(jsConfig.inputFile, gulp.series('copy-js'));
});

