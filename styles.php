<?php
/*
Plugin Name: Styles
Plugin URI: http://stylesplugin.com
Description: Change the appearance of your theme using the <a href="customize.php">WordPress Customizer</a>. Styles changes everything.
Version: 1.1.10
Author: Paul Clark
Author URI: http://pdclark.com
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( version_compare( $GLOBALS['wp_version'], '3.4', '>=' ) ) {

	if ( !defined( 'STYLES_BASENAME' ) ) define( 'STYLES_BASENAME', plugin_basename( __FILE__ ) );
	if ( !defined( 'STYLES_DIR' ) ) define( 'STYLES_DIR', dirname( __FILE__ ) );

	require dirname ( __FILE__ ) . '/classes/styles-plugin.php';

	add_action( 'plugins_loaded', 'Styles_Plugin::get_instance' );

	/**
	 * Additional headers to identify Styles child plugins.
	 * Filter needs to run before plugins_loaded to avoid conflicts
	 *
	 * @return array plugin header search terms
	 */
	function styles_extra_plugin_headers( $headers ) {
		$headers['Styles Class'] = 'styles class';
		$headers['Styles Item'] = 'styles item';
		$headers['Styles Updates'] = 'styles updates';

		return $headers;
	}

	// High priority to avoid conflicts with plugins that don't return the header array correctly.
	add_filter( 'extra_plugin_headers', 'styles_extra_plugin_headers', PHP_INT_MAX );

}else if ( is_admin() ) {

	/**
	 * Notify user if WordPress is older than version 3.4.
	 * @return void
	 */
	function styles_wp_version_notice() {
		echo sprintf(
			'<div class="error"><p>%s<a href="http://codex.wordpress.org/Upgrading_WordPress">%s</a></p></div>',
			__( 'Styles requires WordPress 3.4 or newer.', 'styles' ),
			__( 'Please update.', 'styles' )
		);
	}
	add_action( 'admin_notices', 'styles_wp_version_notice' );

}
