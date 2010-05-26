<?php
/*
Plugin Name: PD Menu
Plugin URI: http://pdclark.com
Description: So amazing.
Version: Beta 0.0.1
Author: Paul Clark
Author URI: http://pdclark.com
*/

/*  Copyright 2010  Paul Clark  (email : support (at) pdclark.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA


	==================
	NOTES FROM AUTHOR:
	==================
	This WordPress API/Framework was created by using many different references including WordPress CODEX, reliable Plugins, and 
	AJAX/jQuery/JavaScript widgets that are already part of the WordPress.  I did not find API/Frameworks that were easy to use and most 
	were incomplete. I was looking for something that would provide me with all the features required to build a good Plugin.  We will
	continue to work on improving and adding new features to this Framework, currently the Plugin includes the following:	

	** What Plugin features are supported? **
	1. 	Activation - Adding options, and new database tables.
	2. 	Deactivation - Removing options and database tables created during activation.
	3.	Menus: New - Create a new menu with submenus, separate from the admin menus.
	4. 	Options Page - A customized options page with additional CSS styles and samples of different <input> values (textarea, text, radio, checkbox, etc.) 
	5. 	Use of AJAX/jQuery/JavaScript Components:
		a) farbtastic -  Is a Color Picker which allows you to select a color in a palate. (http://acko.net/blog/farbtastic-color-picker-released)
		b) 500+ Icons - 16 x 16 and 32 x 32 that can be used in your Plugin.  214 Tango Theme Icons (16 x 16) and (32 x 32) from 
		   FreeDesktop.org ( http://tango.freedesktop.org/Tango_Icon_Library ). There are plenty of icons available on the web, 
		   all you have to do is search for them.  The best resolution comes from icons that are "*.ico" files.  This is because 
		   Icons from .ico files have been designed with 16, 22, 32, 64, etc so they will always display properly. If you use 
		   Photoshop you'll need an .ico converter, Telegraphics offers a free add-on: http://www.telegraphics.com.au/sw/
	6.	Data Entry/List Page - Allows you to Add, Remove, Edit, View data from the database file.  Also offers customized CSS settings
		to improve the appearance of the layout.

	** Upcoming Features **
	1. Menus: Administrative - Add submenus to existing administration menus.  (release date: 11/01/2009).
	2. Updates - Handles automatic updates of new releases (release date: 11/01/2009).
	3. List, View and Edit Database a Table - Allows you to add, remove, edit, view data from a new database file.  (Release date: 11/01/2009).


   =============
   INSTURCTIONS: 
   =============
	Even though this Plugin works without making changes, you need to customize it before you can begin.  Follow the steps below
	before you begin adding your own code.
	
	1.	Change the Standard Plugin Information Header at the beginning of the `pd-menu.php` file, 
		between lines 3 through 8.  A detailed explanation on how to do this can be found in this 
		article: http://codex.wordpress.org/Writing_a_Plugin#File_Headers 
	2.	Choose a <TITLE> for your Plugin, the current <TITLE> for this Plugin is 'PD Menu'.  You can 
		give it any name you want, 'Customer List', 'Time-Slips', 'The Weather Forecaster', etc. 
	3.	Choose a <FILE/FUNCTION> name for this Plugin, the current name is 'pdm'.  Make sure you
		use lower case letters, keep it as short as possible, and remove all special characters and spaces 
		from it.  Following the <TITLE> examples above, you could use, 'customerlist', 'timeslips', 'weatherforcaster'.
	4.	Use your PHP editor and perform the following tasks to all the files within you Plugin directory.  
		- Perform a 'Find and Replace' with 'Match Case' checked, and replace 
		  all instances of 'PD Menu' with the new <TITLE> of your 
		  Plugin.  This <TITLE> is used throughout the Plugin for error messages, 
		  alerts, and notes.
		- Perform a 'Find and Replace' with 'Match Case' checked, and replace all 
		  instances of 'pdm' with the new NAME of your Plugin.
	5. 	Rename the file names below with your new <FILE/FUNCTION> name.
		- 'pd-menu.php' --> <FILE/FUNCTION>.php
		- 'pdm.farbtastic.js' -> <FILE/FUNCTION>.farbtastic.js
	6.	Icons: the '/swpframework/images' directory contains four 'icon' folders that occupy about 13MB of space.  
		We recommend that you move all the icons used in your Plugin from these folders to the '/swpframework/images' 
		directory. Then remove the 'icon.html' file, and all the 'icon' folders that are in the 'images' directory.  
		They have been placed there for your reference, and PD Menu does not use them.  
	7. 	Upload the 'pdm' folder to the '/wp-content/plugins/' directory
	8. 	Rename the 'pdm' of this Plugin from 'pdm' with your new <FILE/FUNCTION> name.
	9. 	Activate the Plugin through the 'Plugins' menu in WordPress
   10. 	When you have completed your Plugin you should:
		a) Remove all the comments I have provided, except for the one that gives me credit for creating this API/Framework.
		b) DO NOT REMOVE: GNU General Public License.
		c) DON'T BE GREEDY - share you Plugin with others through the WordPress Plugin library. ( http://wordpress.org/extend/plugins/about/ )


	=======================================
	WordPress Default Constants & Functions
	=======================================
	I am listing a few extremely important functions, and constants that you need throughout your Plugin. These increase the 
	compatibility of your Plugin.  I have also included links to the WordPress CODEX and Video library for your reference.
	
	"How 'NOT' to Build a WordPress Plugin", by Will Norris( http://wordpress.tv/2009/09/20/will-norris-building-plugins-portland09/ )
	Important Constants and Functions ( http://wpengineer.com/wordpress-return-url/ )
	
	Assigning URL's:
	=======================================
	- plugins_url();   - site_url();
	- content_url();   - admin_url();
	- includes_url();
	- get_bloginfo('template_url');
	
	CSS & JavaScript
	=======================================
	- wp_enqueue_script();	// ( http://codex.wordpress.org/Function_Reference/wp_enqueue_script )
	- wp_enqueue_style();	// ( http://codex.wordpress.org/Function_Reference/wp_enqueue_style )
	                            WordPress Codex: 
	
	Directories, Folders and Paths
	=======================================
	ABSPATH, WP_PLUGIN_DIR, WP_CONTENT_DIR
	
	Actions and Filters visit
	=======================================
	http://codex.wordpress.org/Plugin_API
	http://codex.wordpress.org/Plugin_API/Action_Reference
	http://codex.wordpress.org/Plugin_API/Filter_Reference
	
	WordPress: Release Archive ( Previous Versions of WordPress )
	==============================================================
	The WordPress site has a list of all the WordPress releases.  I suggest you test your Plugin with older versions.
	this API was tested to work with Version 2.6 and up.  We have not tested it with older versions, because the it used
	functions and constants that did not exist before that. 
	
	WordPress: Release Archive - ( http://wordpress.org/download/release-archive/ )

	
*/  


// Sets up Plugin configuration and routing based on names of Plugin folder and files.

# define Plugin constants
define( 'PDM_VERSION', "1.1");			#  Plugin database version: change this value every time you make changes to your plugin. 
define( 'PDM_PURGE_DATA', '1' );		#  When plugin is deactivated, if 'true', all tables, and options will be removed.

define( 'WP_ADMIN_PATH', ABSPATH . '/wp-admin/');  // If you have a better answer to this Constant, feel free to send me an e-mail.  

define( 'PDM_FILE',       basename(__FILE__) );
define( 'PDM_FILE_PATH',  __FILE__);
define( 'PDM_NAME',       basename(__FILE__, ".php") );
define( 'PDM_PATH',       str_replace( '\\', '/', trailingslashit(dirname(__FILE__)) ) );
define( 'PDF_PATH_REL',   str_replace(ABSPATH, '/', PDM_PATH));
define( 'PDM_URL',        plugins_url('', __FILE__) );  // NOTE: It is recommended that every time you reference a url,
														// that you specify the plugins_url('xxx.xxx',__FILE__), WP_PLUGIN_URL,
														// WP_CONTENT_URL, WP_ADMIN_URL view the video by Will Norris.
define( 'PDM_LIB',        PDM_PATH.'/lib');

require_once( PDM_PATH . 'load-css-and-js.php' );
require_once( PDM_PATH . 'functions.php' );
require_once( PDM_PATH . 'menus.php' );
require_once( PDM_PATH . 'pages/options.php' );

// Output DB as XML CSS constants
require_once( PDM_PATH . 'lib/xml_constants/xml_constants.php' );

register_activation_hook(__FILE__,'pdm_activate');  // WordPress Hook that executes the installation
register_deactivation_hook( __FILE__, 'pdm_deactivate' ); // WordPress Hook that handles deactivation of the Plugin.

add_action('plugins_loaded', 'pdm_check_for_updates' );   // Checks if this plugin is an update from a previous version.


## ---------------------------
##	Testing
## --------------------------{
	if (!function_exists('pq')) {
		require_once PDM_LIB.'/phpQuery/phpQuery.php';
	}
	add_theme_support( 'nav-menus' );

	add_action('pd_test', 'pdm_out');

	function pdm_out() {
		$atts = array(
			'container_class' => 'pdn1',
			'echo' => false,
			'before' => '<div>',
			'after' => '</div>',
			'link_before' => '<span>',
			'link_after' => '</span>',
			'menu' => '',
			'menu_class' => 'menu sf-menu',
			#'container' => 'div',
			#'fallback_cb' => 'wp_page_menu',
			#'before_title' => '',
			#'after_title' => '',
		 );

		$menu = pdm_get( $atts );

		echo $menu;
	}
	
	
	/**
	 * Displays a navigation menu.
	 *
	 * Optional $args contents:
	 *
	 * id - The menu id. Defaults to blank.
	 * slug - The menu slug. Defaults to blank.
	 * menu_class - CSS class to use for the div container of the menu list. Defaults to 'menu'.
	 * format - Whether to format the ul. Defaults to 'div'.
	 * fallback_cb - If the menu doesn't exists, a callback function will fire. Defaults to 'wp_page_menu'.
	 * before - Text before the link text.
	 * after - Text after the link text.
	 * link_before - Text before the link.
	 * link_after - Text after the link.
	 * echo - Whether to echo the menu or return it. Defaults to echo.
	 *
	 * @todo show_home - If you set this argument, then it will display the link to the home page. The show_home argument really just needs to be set to the value of the text of the link.
	 *
	 * @since 3.0.0
	 *
	 * @param array $args Arguments
	 */
	function pdm_get( $args = array() ) {
		$defaults = array(
			'menu' => '',
			'container' => 'div',
			'container_class' => '',
			'menu_class' => 'menu',
			'echo' => true,
			'fallback_cb' => 'wp_page_menu',
			'before' => '',
			'after' => '',
			'link_before' => '',
			'link_after' => '',
			'depth' => 0,
			'walker' => '',
			'context' => 'frontend',
		);

		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'pd_nav_menu_args', $args );
		$args = (object) $args;

		if ($args->echo) {
			echo wp_nav_menu($args);
		}else {
			return wp_nav_menu($args);
		}
	}
	
##}end Testing

## ---------------------------
##	Testing AJAX
## --------------------------{
	function pdm_parse_request($wp) {
	    // only process requests with "my-plugin=ajax-handler"
	    if (array_key_exists('pdm', $wp->query_vars) ) {
			switch ($wp->query_vars['pdm']) {
				case 'ajax-handler':
					echo 'Ajax is so handled';
					break;
				case 'get-opts':
					$opts = pdm_get_options();
					FB::log($opts, '$opts');
					break;
				case 'get-css-constants':
					$opts = pdm_get_options();
					
					echo '@constants {'."\r";
					foreach ($opts as $key => $val) {
						if (is_array($val)) {
							foreach ($val as $sub_key => $sub_val) {
								echo "  \${$key}_$sub_key: $sub_val;\r";
							}
						}else {
							echo "  \${$key}: $val;\r";
						}
					}
					echo '}'."\r";
					break;
			}
			exit;
	    }
	}
	add_action('parse_request', 'pdm_parse_request');
	
	function pdm_query_vars($vars) {
	    $vars[] = 'pdm';
	    return $vars;
	}
	add_filter('query_vars', 'pdm_query_vars');
	
##}end Testing AJAX


?>