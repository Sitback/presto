import gutil from 'gulp-util';
import webpack from 'webpack';
import named from 'vinyl-named';
import gulpWebpack from 'webpack-stream';
import UglifyJSPlugin from 'uglifyjs-webpack-plugin';
import * as paths from './paths';


module.exports = (gulp/* , callback */) => {
  return gulp.src(paths.SRC.js)
    .pipe(named())
    .pipe(gulpWebpack({
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
    .pipe(gulp.dest(paths.DEST.js));
};
