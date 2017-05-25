# Presto Theme

This theme provides some starter styling, based off the Bootstrap 3 Drupal 
theme, for all demo functionality included in this profile.

This theme is completely optional and you can use any theme with this profile, 
including those off drupal.org. Do note, you may need to re-place blocks if you 
use a different theme.

Please refer to the `README` file in the _Bootstrap_ theme for more information 
on details of the Bootstrap theme.

## Developing

This theme uses `yarn` to manage all dependencies and `gulp` to handle asset 
compilation, to install these:
* [First, install `yarn` for your OS](https://yarnpkg.com/lang/en/docs/install/)
* Install `gulp` globally: `npm install --global gulp-cli`
* Then install all local dependencies (run this from within the `presto_theme` 
directory): `yarn install`

Then to watch for file changes, just run `gulp` from within the `presto_theme` 
directory.

To compile changes without watching, run `gulp build`.
