<?php
/*
Plugin Name: PD Styles
Plugin URI: http://pdclark.com
Description: Rewrite.
Version: 0.0.2
Author: Paul Clark
Author URI: http://pdclark.com
Author URI: http://pdclark.com

------------------------------------------------------------------------
Copyright 2010  Paul Clark  (email : support (at) pdclark.com)

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


## ---------------------------
##	WP Notes
## --------------------------{
	Inspiration: sivil, gravity, CSScaffold, will norris, mingle
	
	"How 'NOT' to Build a WordPress Plugin", by Will Norris( 	http://wordpress.tv/2009/09/20/will-norris-building-plugins-portland09/ )
	
	###	Paths & URLs
	Important Constants and Functions ( http://wpengineer.com/wordpress-return-url/ )
		plugins_url( $path = '', $plugin = '' ); // ie: __FILE__
		site_url();
		content_url();
		admin_url();
		includes_url();
		get_bloginfo('template_url');
		
		ABSPATH
		WP_PLUGIN_DIR
		WP_CONTENT_DIR

	Actions and Filters
	=======================================
	http://codex.wordpress.org/Plugin_API
	http://codex.wordpress.org/Plugin_API/Action_Reference
	http://codex.wordpress.org/Plugin_API/Filter_Reference
		
	
##}end WP Notes

*/  

// Normally I'd include this in wp-config.php
// It's here so I don't have to get anyone else to install it
// When sharing code
if ( !class_exists('FirePHP') ) {
	ob_start();
	include_once 'lib/FirePHPCore/fb.php';
}

@include dirname ( __FILE__ ) . '/lib/Extension/Observable.php';
@include dirname ( __FILE__ ) . '/lib/Extension/Observer.php';
@include dirname ( __FILE__ ) . '/lib/controllers/Variable/Variable.php';
@include dirname ( __FILE__ ) . '/lib/controllers/Group/Group.php';

/**
 * PD Styles class for common actions between admin and frontend.
 *
 * This class contains all the shared functions required for PD Styles to work
 *
 * @package pd-styles
 * @author pdclark
 **/

class PDStyles extends Scaffold_Extension_Observable {
	
	/**
	 * Plugin Version
	 *
	 * Holds the current plugin version.
	 *
	 * @since 0.1
	 * @var int
	 **/
	var $version = '0.1';
	
	/**
	 * Plugin DB version
	 * 
	 * Holds the current plugin database version. 
	 * Not the same as the current plugin version.
	 * 
	 * @var int
	 **/
	var $db_version = '0.1';
	
	/**
	 * Options array containing all options for this plugin
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $options;
	
	/**
	 * Setup shared functionality between admin and front-end
	 *
	 * @author Matt Martz <matt@sivel.net>
	 * @since 0.1
	 * @return none
	 **/
	function __construct() {
		// ajax hooks so that we can access WordPress within Scaffold
		add_action('parse_request', array( &$this, 'parse_request') );
		add_filter('query_vars', array( &$this, 'query_vars') );
		
		$this->load_extensions( $this->plugin_dir_path() . 'extensions' );
	}
	
	/**
	 * Get specific option from the options table
	 * 
	 * @author Matt Martz <matt@sivel.net>
	 * @param string $option Name of option option to be used as an array key for retreiving the specific value
	 * @since 0.1
	 * @return mixed
	 **/
	function get_option($option) {
		if ( isset( $this->options[$option] ) ) {
			return $this->options[$option];
		}else {
			return false;
		}
	}
	
	/**
	 * Get the full URL to the plugin
	 *
	 * @author Matt Martz <matt@sivel.net>
	 * @return string
	 * @since 0.1
	 */
	function plugin_url() {
		$plugin_url = plugins_url ( plugin_basename ( dirname ( __FILE__ ) ) );
		return $plugin_url;
	}
	
	/**
	 * Get the full path to the plugin (with trailing slash)
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function plugin_dir_path() {
		return plugin_dir_path( __FILE__ );
	}
	
	/**
	 * Strip ABSPATH from a path.
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function get_relative_path( $path ) {
		$path = str_replace( ABSPATH, '/', $path );
		return $path;
	}
	
	/**
	 * Convert CSS path into a form appropriate for use as a database key
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function get_css_permalink( $path ) {
		$blacklist = array(
			'[',
			']',
			'"',
			"'",
			'>',
			'<'
		);
		
		$path = str_replace($blacklist, '-', self::get_relative_path( $path ) );
		
		return $path;
	}
	
	/**
	 * Return an md5 based off of the current options of the plugin and the
	 * current version of the plugin.
	 *
	 * This is used for creating unique cache files and for cache busting.
	 *
	 * @author Matt Martz <matt@sivel.net>
	 * @since 0.1
	 * @return string
	 */
	function md5() {
		return md5 ( serialize ( $this->options ) . $this->version );
	}
	
	/**
	 * Deactivate this plugin and die
	 *
	 * Used to deactivate the plugin when files critical to it's operation can not be loaded
	 *
	 * @author Matt Martz <matt@sivel.net>
	 * @since 0.1
	 * @return none
	 */
	function deactivate_and_die( $file ) {
		load_plugin_textdomain ( 'pd-styles' , false , 'pd-styles/localization' );
		$message = sprintf ( __( "PD Styles has been automatically deactivated because the file <strong>%s</strong> is missing. Please reinstall the plugin and reactivate." ) , $file );
		if ( ! function_exists ( 'deactivate_plugins' ) ) {
			include ( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		deactivate_plugins ( __FILE__ );
		wp_die ( $message );
	}
	
	/**
	 * Load and output a view file. Pass it some data somehow.
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function load_view($view) {
		
		$file = dirname ( __FILE__ ) . '/lib/views/'.$view;
		
		if ( ! @include ( $file ) ) {
			_e ( sprintf ( '<div id="message" class="updated fade"><p>The file <strong>%s</strong> is missing.  Please reinstall the plugin.</p></div>' , $file ), 'pd-styles' );
		}
	}
	
	/**
	 * Pass ?scaffold&file=file.css requests to CSS Scaffold
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function parse_request( $wp ) {
	    // only process requests with "?scaffold"
	    if (isset( $_GET['scaffold'] ) ) {
			
			$this->load_extensions( $this->plugin_dir_path() . 'extensions' );
			
			$this->options = get_option( 'pd-styles' );

			$config = $this->get_scaffold_config();
			
			if ( isset( $_GET['preview'] ) ) {
				$config['PDStyles']['preview'] = true;
			}
			
			$system = $this->plugin_dir_path() . 'scaffold'; // No trailing slash

			$scaffold_include = $system.'/index.php';
			if ( ! @include( $scaffold_include ) ) {
				exit( 'Could not find ' . $scaffold_include );
			}

			exit;
	    }
	}
	
	/**
	 * Whitelist the scaffold query var for parse_request()
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function query_vars($vars) {
	    $vars[] = 'scaffold';
	    return $vars;
	}
	
	/**
	 * Default scaffold config values
	 * 
	 * @since 0.1
	 * @return array
	 **/
	function get_scaffold_config() {
		
		// See scaffold/parse.php for full scaffold config documentation
		$s = $this->get_option('scaffold');
		$config =& $s['config'];
		
		// Minify CSS when in production
		if ( $config['production'] === true ) {
			$config['extensions'][] = 'Minify';
		}
		
		return $config;
	}
	
	/**
	 * Loads the extension files
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function load_extensions( $path /*,$scaffold*/) {	
		# Scaffold_Helper object
		// $helper = $this->getHelper();
		
		# The glob path
		$path = realpath($path) . DIRECTORY_SEPARATOR . '*';
	
		# Load each of the extensions
		foreach(glob($path) as $ext)
		{			
			$ext .= DIRECTORY_SEPARATOR;
		
			$config 	= array();
			$name 		= basename($ext);
			$class 		= 'PDStyles_Extension_' . $name;
			$file 		= $ext.$name.'.php';
			
			# This extension isn't enabled
			//if(!in_array($name, $this->options['extensions']))
			//	continue;
			
			# Get the config for the extension if available
			//if(isset($this->options[$name]))
			//	$config = $this->options[$name];
			
			# Load the controller
			if(file_exists($file))
			{
				require_once realpath($ext.$name.'.php');
				$object = new $class($config);
				// $object->attach_helper($helper);
				$this->attach($name,$object);
			}
		}
		// return $scaffold;
	}
	
} // END PDStyles class

/**
 * Instantiate the PDStylesFrontend or $PDStylesAdminController Class
 *
 * Deactivate and die if files can not be included
 */
if ( is_admin () ) {
	
	// include admin class

	if ( @include dirname ( __FILE__ ) . '/lib/controllers/PDStylesAdminController.php' ) {
		
		$PDStylesAdminController = new PDStylesAdminController ();

	} else {
		PDStyles::deactivate_and_die ( dirname ( __FILE__ ) . '/inc/admin.php' );
	}
} else {
	
	// include subadmin class
	
	if ( @include dirname ( __FILE__ ) . '/lib/controllers/PDStylesFrontendController.php' ) {
		$PDStylesFrontendController = new PDStylesFrontendController ();
	} else {
		PDStyles::deactivate_and_die ( dirname ( __FILE__ ) . '/inc/front-end.php' );
	}
} 

?>