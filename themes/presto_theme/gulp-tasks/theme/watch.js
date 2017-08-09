import browserSync from 'browser-sync';
import * as paths from '../paths';

module.exports = {
  deps: ['scss', 'js'],
  fn(gulp/* , callback */) {
    browserSync({
      proxy: 'http://presto.docker',
      host: 'presto.docker',
      open: false,
      socket: {
        domain: 'localhost:3000'
      },
      snippetOptions: {
        rule: {
          match: /<\/body>/i,
          fn: (snippet) => snippet
        }
      }
    });

    gulp.watch(paths.SRC.scss, ['scss']);
    gulp.watch(paths.SRC.js, ['js']);
  }
};
