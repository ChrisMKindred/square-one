const gulp = require( 'gulp' );
const cssnano = require( 'gulp-cssnano' );
const rename = require( 'gulp-rename' );
const sourcemaps = require( 'gulp-sourcemaps' );
const pkg = require( '../package.json' );

function minify( src = [], dest = pkg.square1.paths.core_admin_css_dist ) {
	return gulp.src( src )
		.pipe( sourcemaps.init() )
		.pipe( cssnano( { zindex: false } ) )
		.pipe( rename( {
			suffix: '.min',
			extname: '.css',
		} ) )
		.pipe( sourcemaps.write( '.' ) )
		.pipe( gulp.dest( dest ) );
}

module.exports = {
	themeMin() {
		return Promise.resolve( 'Deprecated' );
	},
	themeComponentsMin() {
		return Promise.resolve( 'Deprecated' );
		return minify( [
			`${ pkg.square1.paths.core_theme_css }components.css`,
		], pkg.square1.paths.core_theme_css_dist );
	},
	themeIntegrationsMin() {
		return Promise.resolve( 'Deprecated' );
		return minify( [
			`${ pkg.square1.paths.core_theme_css }integrations.css`,
		], pkg.square1.paths.core_theme_css_dist );
	},
	themeLegacyMin() {
		return Promise.resolve( 'Deprecated' );
		return minify( [
			`${ pkg.square1.paths.core_theme_css }legacy.css`,
		], pkg.square1.paths.core_theme_css_dist );
	},
	themeWPEditorMin() {
		return Promise.resolve( 'Deprecated' );
		return minify( [
			`${ pkg.square1.paths.core_admin_css }editor-style.css`,
		] );
	},
	themeWPAdminMin() {
		return Promise.resolve( 'Deprecated' );
		return minify( [
			`${ pkg.square1.paths.core_admin_css }master.css`,
		] );
	},
	themeWPLoginMin() {
		return Promise.resolve( 'Deprecated' );
		return minify( [
			`${ pkg.square1.paths.core_admin_css }login.css`,
		] );
	},
};
