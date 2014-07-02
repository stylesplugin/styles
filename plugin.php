<?php
/*
Plugin Name: Styles: Font Menu
Plugin URI: http://github.com/stylesplugin/styles-font-menu
Description: Display an up-to-date menu of Google Fonts. Include it in your own plugins and themes, or install as a plugin for testing and a live demo. Uses the Chosen library to allow menu search and styles.
Version: 1.0.2
Author: Brainstorm Media
Author URI: http://brainstormmedia.com
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * Only include library in admin by default. Override with the filter
 *
 * @example add_filter( 'styles_font_menu_include_on_frontend', '__return_true' );
 */
if (
	apply_filters( 'styles_font_menu_include_on_frontend', is_admin() )
	&& !class_exists( 'SFM_Plugin' )
	&& version_compare( $GLOBALS['wp_version'], '3.4', '>=' )
) {

	require_once dirname( __FILE__ ) . '/classes/sfm-plugin.php';

	if ( did_action( 'init' ) ) {
		SFM_Plugin::get_instance();
	}else {
		add_action( 'init', 'SFM_Plugin::get_instance' );
	}

}else if (
	version_compare( $GLOBALS['wp_version'], '3.4', '<' )
	&& apply_filters( 'styles_font_menu_exit_on_php_version_error', true )
	&& !function_exists( 'styles_font_menu_wp_version_notice' )
) {

	/**
	 * Exit and warn by default. Use the filter to disable exiting,
	 * or add your own behavior and return false.
	 *
	 * @example add_filter( 'styles_font_menu_include_on_frontend', '__return_false' );
	 */
	function styles_font_menu_wp_version_notice() {
		echo sprintf(
			'<div class="error"><p>%s<a href="http://codex.wordpress.org/Upgrading_WordPress">%s</a></p></div>',
			__( 'Styles Font Menu requires WordPress 3.4 or newer.', 'styles-font-menu' ),
			__( 'Please update.', 'styles-font-menu' )
		);
	}
	add_action( 'admin_notices', 'styles_font_menu_wp_version_notice' );

}