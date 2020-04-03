const { resolve } = require( 'path' );
const webpack = require( 'webpack' );
const merge = require( 'webpack-merge' );
const common = require( './common.js' );
const rules = require( './rules.js' );
const vendor = require( './vendors' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const BundleAnalyzerPlugin = require( 'webpack-bundle-analyzer' ).BundleAnalyzerPlugin;
const pkg = require( '../package.json' );

module.exports = merge( common, {
	cache: true,
	mode: 'development',
	entry: {
		scripts: `./${ pkg.square1.paths.core_admin_js_src }index.js`,
		vendor: vendor.admin,
	},
	output: {
		filename: '[name].js',
		chunkFilename: '[name].[chunkhash].js',
		path: resolve( `${ __dirname }/../`, pkg.square1.paths.core_admin_js_dist ),
		publicPath: `/${ pkg.square1.paths.core_admin_js_dist }`,
	},
	devtool: 'eval-source-map',
	module: {
		rules: [
			rules.miniExtractPlugin,
		],
	},
	plugins: [
		new MiniCssExtractPlugin( {
			filename: '../../css/admin/[name].css',
		} ),
		new webpack.LoaderOptionsPlugin( {
			debug: true,
		} ),
		new BundleAnalyzerPlugin( {
			analyzerMode: 'static',
			reportFilename: resolve( `${ __dirname }/../`, 'reports/webpack-admin-bundle.html' ),
			openAnalyzer: false,
		} ),
	],
	optimization: {
		splitChunks: { // CommonsChunkPlugin()
			name: 'vendor',
			minChunks: 2,
		},
		noEmitOnErrors: true, // NoEmitOnErrorsPlugin
		concatenateModules: true, //ModuleConcatenationPlugin
	},
} );
