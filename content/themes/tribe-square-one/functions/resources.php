<?php
/**
 * Functions: Front-end Resources (CSS & JS)
 *
 * Functions for handling scripts, styles, and any other needed resources
 *
 * @since tribe-square-one 1.0
 */


// Login Resources
add_action( 'login_enqueue_scripts', 'login_styles' );

// Site Resources
add_action( 'wp_enqueue_scripts', 'enqueue_styles' );
add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );

// Site Fonts
add_action( 'wp_head', 'tribe_fonts' );

// Site Resource Optimizations
add_filter( 'wp_default_scripts', 'tribe_remove_jquery_migrate' );
add_filter( 'script_loader_src', 'tribe_remove_static_resource_version', 15, 1 );
add_filter( 'style_loader_src', 'tribe_remove_static_resource_version', 15, 1 );


/**
 * Gets the current site version.
 *
 * If the Git_Info Plugin is active, it grabs the latest Git hash.
 * If not, it uses the give default.
 *
 * @param string $default
 *
 * @return string
 */

function tribe_get_version( $default = '1.0' ) {

    if ( ! class_exists( '\Git_Info_Plugin' ) ) {
        return $default;
    }

    $version = wp_cache_get( 'tribe_site_version' );

    if ( empty( $version ) ) {
        $version = \Git_Info_Plugin::get_instance()->get_revision();
        wp_cache_set( 'tribe_site_version', $version );
    }

    return $version;
}


/**
  * Add a stylesheet to the login page
  */

function login_styles() {

    $css_dir = trailingslashit( get_template_directory_uri() ) . 'css/admin/';
    $version = tribe_get_version();

    // CSS
    $css_login = 'login.css';

    // Production
    if ( ! defined( 'SCRIPT_DEBUG' ) || SCRIPT_DEBUG === false ) {
        $css_login = 'dist/login.min.css';
    }

    wp_enqueue_style( 'tribe-theme-login', $css_dir . $css_login, $version );

}


/**
 * Enqueue styles
 */

function enqueue_styles() {

    $css_dir = trailingslashit( get_template_directory_uri() ) . 'css/';
    $version = tribe_get_version();

    // CSS
    $css_global = 'master.css';
    $css_print  = 'print.css';

    // Production
    if ( ! defined( 'SCRIPT_DEBUG' ) || SCRIPT_DEBUG === false ) {
        $css_global = 'dist/master.min.css';
        $css_print  = 'dist/print.min.css';
    }

    // CSS: base
    wp_enqueue_style( 'tribe-theme-base', $css_dir . $css_global, array(), $version, 'all' );

    // CSS: print
    wp_enqueue_style( 'tribe-theme-print', $css_dir . $css_print, array(), $version, 'print' );

}


/**
 * Enqueue scripts
 */

function enqueue_scripts() {

    $js_dir  = trailingslashit( get_template_directory_uri() ) . 'js/';
    $version = tribe_get_version();

    // JS
    $scripts     = 'scripts.js';
	$script_deps = array( 'babel-polyfill' );

	wp_deregister_script('jquery');

    // Production
    if ( ! defined( 'SCRIPT_DEBUG' ) || SCRIPT_DEBUG === false ) {
        $scripts     = 'dist/master.min.js';
	    $script_deps = array();
    } else {
	    wp_enqueue_script( 'babel-polyfill', $js_dir . 'browser-polyfill.js', array(), $version, true );
    }

    wp_enqueue_script( 'tribe-theme-modernizr', $js_dir . 'modernizr.js', '', $version, false );
    wp_enqueue_script( 'tribe-theme-scripts', $js_dir . $scripts, $script_deps, $version, true );

    wp_localize_script( 'tribe-theme-scripts' , 'modern_tribe_i18n', tribe_js_i18n() );
    wp_localize_script( 'tribe-theme-scripts' , 'modern_tribe_config', tribe_js_config() );

    wp_enqueue_script( 'tribe-theme-scripts' );

    // JS: Comments
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
        wp_enqueue_script( 'comment-reply' );

}


/**
  * Add any required fonts
  */

function tribe_fonts() {


}


/**
 * Remove WordPress jQuery migrate script (production only)
 */

function tribe_remove_jquery_migrate( $scripts ) {
    if( is_admin() )
        return $scripts;
    if ( ! defined( 'SCRIPT_DEBUG' ) || SCRIPT_DEBUG === false ) {
        $scripts->remove( 'jquery' );
        $scripts->add( 'jquery', false, array( 'jquery-core' ), '1.11.1' );
    } else {
        return $scripts;
    }
}


/**
 * Remove WordPress static resource version number (production only)
 */

function tribe_remove_static_resource_version( $resources ) {
    if( is_admin() )
        return $resources;
    if ( ! defined( 'SCRIPT_DEBUG' ) || SCRIPT_DEBUG === false )
        return remove_query_arg( 'ver', $resources );
    else 
        return $resources;
}


/**
 * Provides config data to be used by front-end JS
 *
 * @return array
 */

function tribe_js_config() {

    static $data = array();
    if ( empty( $data ) ) {
        $data = array(
            'images_url'   => trailingslashit( get_template_directory_uri() ) . 'img/',
            'template_url' => trailingslashit( get_template_directory_uri() ),
            'home_url'     => esc_url( home_url( '/' ) )
        );
        $data = apply_filters( 'tribe_js_config', $data );
    }
    return $data;

}

