import sass from 'gulp-sass';
import clean from 'gulp-clean-css';
import browserSync from 'browser-sync';
import sourcemaps from 'gulp-sourcemaps';
import autoprefixer from 'gulp-autoprefixer';
import * as paths from './paths';

module.exports = (gulp/* , callback */) => {
  return gulp.src(paths.SRC.scss)
    .pipe(sourcemaps.init())
    .pipe(sass({
      outputStyle: 'compressed',
      includePaths: [paths.SRC.bootstrap]
    }))
    .on('error', sass.logError)
    .pipe(autoprefixer({
      browsers: ['last 2 versions', 'iOS 8'],
      cascade: false
    }))
    .pipe(clean())
    .pipe(sourcemaps.write(paths.DEST.maps))
    .pipe(gulp.dest(paths.DEST.css))
    .pipe(browserSync.stream());
};
