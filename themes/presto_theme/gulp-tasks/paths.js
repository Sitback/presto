/* global __dirname */

import path from 'path';

export const DEST = Object.freeze({
  js: path.resolve(__dirname, '..', 'js', 'compiled'),
  css: path.resolve(__dirname, '..', 'css'),
  // Needs to be relative to the two above paths.
  maps: 'maps',
  images: path.resolve(__dirname, '..', 'images'),
  fonts: path.resolve(__dirname, '..', 'font')
});

export const SRC = Object.freeze({
  js: path.resolve(__dirname, '..', 'js', 'src', '**', '*.js'),
  scss: path.resolve(__dirname, '..', 'scss', '**', '*.scss'),
  images: path.resolve(__dirname, '..', 'images'),
  fonts: path.resolve(__dirname, '..', 'fonts'),
  bootstrap: path.resolve(__dirname, '..', 'node_modules', 'bootstrap-sass', 'assets', 'stylesheets')
});
