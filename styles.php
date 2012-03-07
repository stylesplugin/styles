<?php
/*
Plugin Name: Styles
Plugin URI: http://stylesplugin.com
Description: Change the appearance of your theme using the WordPress admin. Creates WordPress theme options for images, colors, gradients, and fonts.
Version: 0.5.1
Author: Brainstorm Media
Author URI: http://brainstormmedia.com
*/

/**
 * This plugin is made possible in part by code reuse from the following authors:
 * 
 *   Anthony Short, Scaffold: https://github.com/anthonyshort/Scaffold
 *   Matt Martz (Sivel), Shadowbox: http://profiles.wordpress.org/sivel/
 *   Joost de Valk (Yoast), WordPress SEO: http://wordpress.org/extend/plugins/wordpress-seo/
 * 
 * Thank you for being generous with your knowledge.
 *
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 * 
 * Copyright 2010 Brainstorm Media
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

/**
 * PHP version check based on WP SEO by Joost de Valk http://yoast.com/wordpress/seo/
 */
if ( version_compare(PHP_VERSION, '5.2', '<') ) {
	if ( is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX) ) {
		require_once ABSPATH.'/wp-admin/includes/plugin.php';
		deactivate_plugins( __FILE__ );
	   wp_die( sprintf( __('Styles requires PHP 5.2 or higher, as does WordPress 3.2 and higher. The plugin has now disabled itself. For more info, %s$1see this post%s$2.', 'Styles'), '<a href="http://codex.wordpress.org/Switching_to_PHP5">', '</a>') );
	}
}

/**
 * Instantiate the $StormStyles object
 */
function storm_styles_init() {
	
	require dirname ( __FILE__ ) . '/classes/stormFirePHP/stormFirePHP.php';
	require dirname ( __FILE__ ) . '/classes/storm-styles.php';
	require dirname ( __FILE__ ) . '/classes/storm-wp-frontend.php';
	
	if ( is_admin() || DOING_AJAX ) {
		// Only load heavy files if we're in wp-admin or processing CSS over AJAX
		require dirname ( __FILE__ ) . '/classes/storm-css-processor.php';
		require dirname ( __FILE__ ) . '/classes/storm-wp-settings.php';
		require dirname ( __FILE__ ) . '/classes/storm-wp-admin.php';
	}
	
	$storm_styles = new Storm_Styles();
	
}
add_action('init', 'storm_styles_init');




