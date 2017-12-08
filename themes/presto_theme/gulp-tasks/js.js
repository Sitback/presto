import gutil from 'gulp-util';
import ignore from 'gulp-ignore';
import webpack from 'webpack';
import named from 'vinyl-named';
import gulpWebpack from 'webpack-stream';
import UglifyJSPlugin from 'uglifyjs-webpack-plugin';
import * as paths from './paths';
import path from 'path';

function condition(file) {
    console.log(file.stem);
    return true;
}

module.exports = (gulp/* , callback */) => {
    return gulp.src(paths.SRC.js)
        .pipe(named())
        .pipe(gulpWebpack({
            devtool: 'source-map',
            plugins: [
                new UglifyJSPlugin({
                    sourceMap: true,
                    parallel: true,
                    extractComments: true,
                    compress: false,
                    mangle: false,
                    output: {
                        beautify: true,
                        preamble: `/* Presto JS -- built ${new Date()}*/`
                    }
                }),
                new webpack.LoaderOptionsPlugin({
                    options: {
                        eslint: {
                            configFile: path.join(__dirname, '../.eslintrc.json'),
                            useEslintrc: false
                        }
                    }
                })
            ],
            module: {
                rules: [
                    {
                        enforce: 'pre',
                        test: /\.js/,
                        exclude: /(node_modules|bower_components|typekit-dev.js)/,
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
                        }
                    }
                ]
            }
        }, webpack))
        .on('error', function(err) {
            // This will catch the webpack/gulp issue that closes the process on an error.
            this.emit('end');
        })
        .pipe(gulp.dest(paths.DEST.js))
        .pipe(ignore.exclude('*.map'))
        .pipe(ignore.exclude('*.LICENSE'))
        .pipe(named(function(file) {
            return `${file.stem}.min`;
        }))
        .pipe(gulpWebpack({
            devtool: 'source-map',
            plugins: [
                new UglifyJSPlugin({
                    mangle: true,
                    sourceMap: true,
                    parallel: true
                })
            ]
        }, webpack))
        .on('error', function(err) {
            // This will catch the webpack/gulp issue that closes the process on an error.
            this.emit('end');
        })
        .pipe(gulp.dest(paths.DEST.js))
        ;
};
