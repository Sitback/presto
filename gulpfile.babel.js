import gulp from 'gulp';
import path from 'path';
import gutil from 'gulp-util';
import webpack from 'webpack';
import through from 'through2';
import named from 'vinyl-named';
import gulpWebpack from 'webpack-stream';
import gulpRequireTasks from 'gulp-require-tasks';
import UglifyJSPlugin from 'uglifyjs-webpack-plugin';

const CWD = process.cwd();
const JS_SRC = ['js/src/**/*.js', 'modules/**/js/src/**/*.js'];
// Relative to the JS src dir.
const JS_DEST = ['..', 'compiled'];

// Add theme tasks too.
gulpRequireTasks({
  path: `${CWD}/themes/presto_theme/gulp-tasks`
});

let originalPaths = {};
let generateJsWebpackTask = (watch = true) => {
  return gulp.src(JS_SRC)
    .pipe(through.obj((file, enc, cb) => {
      // Keep track of the file's original path since 'webpack-stream' builds
      // a new vinyl object and loses the original path :/.
      // @see shama/webpack-stream#116
      originalPaths[path.basename(file.path)] = path.dirname(file.path);
      cb(null, file);
    }))
    .pipe(named())
    .pipe(gulpWebpack({
      watch,
      devtool: 'sourcemap',
      module: {
        rules: [
          {
            enforce: 'pre',
            test: /\.js/,
            exclude: /(node_modules|bower_components)/,
            use: {
              loader: 'eslint-loader',
              options: {
                cache: true
              }
            }
          },
          {
            test: /\.js$/,
            exclude: /(node_modules|bower_components)/,
            use: {
              loader: 'babel-loader',
              options: {
                presets: ['env']
              }
            }
          }
        ]
      },
      plugins: [
        new UglifyJSPlugin({
          mangle: false
        })
      ]
    }, webpack))
    .on('error', function (err) {
      gutil.log('WEBPACK ERROR', err.message);
    })
    .pipe(gulp.dest((file) => {
      // Output to original path or just base if we don't have a stored one.
      let filename = path.basename(file.path);
      let filepath = file.base;
      if (originalPaths.hasOwnProperty(filename)) {
        filepath = originalPaths[filename];
      }
      return path.resolve(filepath, ...JS_DEST);
    }));
};

gulp.task('js:watch', () => {
  return generateJsWebpackTask();
});

gulp.task('js:build', () => {
  return generateJsWebpackTask(false);
});

gulp.task('default', ['js:watch']);
