'use strict';

import gulp from 'gulp';
import gulpRequireTasks from 'gulp-require-tasks';

const CWD = process.cwd();

gulpRequireTasks({
  path: `${CWD}/gulp-tasks`
});

gulp.task('default', ['theme:watch']);
