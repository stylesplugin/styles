<?php
/*
Plugin Name: Styles
Plugin URI: http://stylesplugin.com
Description: Change the appearance of your theme using the <a href="customize.php">WordPress Customizer</a>. Styles changes everything.
Version: 1.0.10
Author: Brainstorm Media
Author URI: http://brainstormmedia.com
*/

/**
 * Copyright 2013 Brainstorm Media
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

function styles_plugin_init() {
	global $storm_styles;

	if ( is_admin() ) {
		global $wp_version;
		$styles_exit_msg = esc_html__( 'Styles requires WordPress 3.4 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update.</a>', 'styles' );
		if ( version_compare( $wp_version, "3.4", "<" ) ) {
			exit( $styles_exit_msg );
		}
	}

	if ( !defined( 'STYLES_BASENAME' ) ) define( 'STYLES_BASENAME', plugin_basename( __FILE__ ) );
	if ( !defined( 'STYLES_DIR' ) ) define( 'STYLES_DIR', dirname( __FILE__ ) );

	require dirname ( __FILE__ ) . '/classes/styles-plugin.php';
	
	$storm_styles = new Styles_Plugin();

}
add_action( 'plugins_loaded', 'styles_plugin_init' );