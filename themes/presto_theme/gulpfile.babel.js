'use strict';

import autoprefixer from 'gulp-autoprefixer';
import browserSync from 'browser-sync';
import clean from 'gulp-clean-css';
import gulp from 'gulp';
import gutil from 'gulp-util';
import sass from 'gulp-sass';
import sourcemaps from 'gulp-sourcemaps';
import uglify from 'gulp-uglify';
import browserify from 'browserify';
import babelify from 'babelify';
import globby from 'globby';
import source from 'vinyl-source-stream';
import buffer from 'vinyl-buffer';
import through from 'through2';

const OUTPUT_JS_FILE = 'presto.min.js';

const src = {
  js: 'js/src/**/*.js',
  scss: 'scss/**/*.scss',
  images: 'images',
  fonts: 'fonts',
  bootstrap: 'node_modules/bootstrap-sass/assets/stylesheets'
};

const dest = {
  js: 'js/compiled',
  css: 'css',
  maps: 'maps',
  images: 'images',
  fonts: 'fonts'
}


// Development
// ------------------------------------------------------
gulp.task('development', ['js', 'scss'], () => {
  browserSync({
    proxy: 'http://presto.docker',
    host: 'presto.docker',
    open: false,
    socket: {
        domain: "localhost:3000"
    },
    'snippetOptions': {
      'rule': {
        'match': /<\/body>/i,
        'fn': (snippet) => snippet
      }
    }
  });

  gulp.watch(src.scss, ['scss']);
  gulp.watch(src.js, ['js']);
});


// Sass
// ------------------------------------------------------
gulp.task('scss', () => {
  return gulp.src(src.scss)
    .pipe(sourcemaps.init())
    .pipe(sass({
      outputStyle: 'compressed',
      includePaths: [src.bootstrap]
    }))
    .on('error', sass.logError)
    .pipe(autoprefixer({
      browsers: ['last 2 versions', 'iOS 8'],
      cascade: false
    }))
    .pipe(clean())
    .pipe(sourcemaps.write(dest.maps))
    .pipe(gulp.dest(dest.css))
    .pipe(browserSync.stream());
});


// Javascript
// ------------------------------------------------------
gulp.task('js', () => {
  let bundledStream = through();

  bundledStream
    .pipe(source(OUTPUT_JS_FILE))
    .pipe(buffer())
    .pipe(sourcemaps.init({ loadMaps: true }))
    .pipe(uglify({
      mangle: false
    }))
    .on('error', gutil.log)
    .pipe(sourcemaps.write(dest.maps))
    .pipe(gulp.dest(dest.js));

  globby([src.js]).then((entries) => {
    let bundler = browserify({
      entries,
      debug: true,
      transform: [ babelify ]
    })
    // Drupal adds this in itself.
    .ignore('jquery');

    bundler.bundle().pipe(bundledStream);
  }).catch((err) => {
    bundledStream.emit('error', err);
  });

  return bundledStream;
});


// Tasks
// ------------------------------------------------------
gulp.task('default', ['development']);
gulp.task('build', ['scss', 'js']);
