<?php
/*
Plugin Name: Styles
Plugin URI: http://stylesplugin.com
Description: Change the appearance of your theme using the WordPress admin. Creates WordPress theme options for images, colors, gradients, and fonts.
Version: 0.4.1
Author: Brainstorm Media
Author URI: http://brainstormmedia.com
Inspiration: sivil, gravity, CSS Scaffold, will norris, mingle
Contributors: Anthony Short, sivil

------------------------------------------------------------------------
Copyright 2010  Paul Clark  (email : pdclark (at) brainstormmedia.com)

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

*/  

include dirname ( __FILE__ ) . '/classes/stormFirePHP/stormFirePHP.php';
include dirname ( __FILE__ ) . '/classes/scaffold-bare/Observable.php';
include dirname ( __FILE__ ) . '/classes/scaffold-bare/Observer.php';
include dirname ( __FILE__ ) . '/classes/StormStylesObserver.php';
include dirname ( __FILE__ ) . '/classes/StormCSSParser.php';
include dirname ( __FILE__ ) . '/classes/Variable.php';
include dirname ( __FILE__ ) . '/classes/Group.php';


/**
 * Common actions between admin and front-end.
 *
 * This class contains all the shared functions required for StormStyless to work
 *
 * @package StormStyles
 * @author pdclark
 **/

class StormStyles extends Scaffold_Extension_Observable {
	
	/**
	 * Plugin Version
	 *
	 * Holds the current plugin version.
	 *
	 * @since 0.1
	 * @var int
	 **/
	var $version = '0.4.1';
	
	/**
	 * Plugin DB version
	 * 
	 * Holds the current plugin database version. 
	 * Not the same as the current plugin version.
	 * 
	 * @var int
	 **/
	var $db_version = '0.4';
	
	/**
	 * Options array containing all options for this plugin
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $options;
	
	/**
	 * Full file system path to the main plugin file
	 *
	 * @since 0.1
	 * @var string
	 */
	var $plugin_file;

	/**
	 * Path to the main plugin file relative to WP_CONTENT_DIR/plugins
	 *
	 * @since 0.1
	 * @var string
	 */
	var $plugin_basename;
	
	/**
	 * Path to CSS file being manipulated
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $file = false;
	
	/**
	 * Setup shared functionality between admin and front-end
	 *
	 * @author Matt Martz <matt@sivel.net>
	 * @since 0.1
	 * @return none
	 **/
	function __construct() {
		$this->register_scripts();
		$this->load_extensions( $this->plugin_dir_path() . 'gui' );

		// ajax hooks so that we can access WordPress within Scaffold
		add_action('parse_request', array( &$this, 'parse_request') );
		add_filter('query_vars', array( &$this, 'query_vars') );
		
		// If we loaded CSS through @import, tell WordPress to dequeue
		add_action( 'wp_print_styles', array( &$this , 'dequeue_at_imports' ), 0);
		
		// search for and process enqueued SCSS files
		add_action( 'wp_print_styles', array( &$this , 'build' ), 9999);
		add_action( 'admin_print_styles', array( &$this , 'build' ), 0);

		// Frontend
		add_action ( 'template_redirect' , array( &$this , 'frontend_js' ) );
		
		// Admin Bar
		add_action( 'admin_bar_menu', array( &$this, 'admin_bar' ), 95 );

		// Full path and plugin basename of the main plugin file
		$this->plugin_file = __FILE__;
		$this->plugin_basename = plugin_basename ( $this->plugin_file );

	}
	
	function admin_bar() {
		global $wp_admin_bar;
		if ( current_user_can('manage_options') && is_admin_bar_showing() ) {
			$wp_admin_bar->add_menu( array( 'parent' => 'appearance', 'id' => 'storm-styles', 'title' => __( 'Styles' ), 'href' => admin_url('themes.php?page=StormStyles'), ) );
		}
		
	}
	
	/**
	 * @since 0.1
	 * @return void
	 **/
	function register_scripts() {
		wp_register_script('storm-colorpicker'    , $this->plugin_url().'/js/colorpicker/js/colorpicker.js',array('jquery'), $this->version, true);
		wp_register_script('storm-jq-ui-slider'   , $this->plugin_url().'/js/jquery.ui.slider.min.js'      ,array('jquery', 'jquery-ui-core' ), $this->version, true);
		wp_register_script('storm-gradient-picker', $this->plugin_url().'/js/jq.gradientpicker.js'         ,array('storm-jq-ui-slider', 'storm-colorpicker' ), $this->version, true);
		wp_register_script('jqcookie'             , $this->plugin_url().'/js/jquery.cookie.js'             ,array('jquery'), $this->version, true);

		wp_register_script('storm-admin-main'     , $this->plugin_url().'/js/admin.js'                ,array('jqcookie', 'storm-gradient-picker', 'storm-jq-ui-slider', 'storm-colorpicker', 'thickbox', 'media-upload' ), $this->version, true);
	}
	
	/**
	 * Enqueue javascript required for the front-end editor
	 * 
	 * @return none
	 * @since 0.1
	 */
	function frontend_js() {
		
		if ( !current_user_can ( 'manage_options' ) ) {
			return;
		}

		// Live Preview
		wp_enqueue_script('storm-frontend', $this->plugin_url().'/js/frontend-main.js', array('jqcookie', 'jquery', ), $this->version, true);
		
		wp_localize_script ( 'storm-frontend' , 'storm_frontend' , array(
			'ajaxurl'	 => admin_url('admin-ajax.php') ,
		) );
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
		$plugin_url = plugins_url ( basename( dirname( __FILE__) ) );
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
		load_plugin_textdomain ( 'StormStyles' , false , 'StormStyles/localization' );
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
		
		$file = dirname ( __FILE__ ) . '/views/'.$view;
		
		if ( ! @include ( $file ) ) {
			_e ( sprintf ( '<div id="message" class="updated fade"><p>The file <strong>%s</strong> is missing.  Please reinstall the plugin.</p></div>' , $file ), 'StormStyles' );
		}
	}
	
	/**
	 * Pass ?scaffold&file=file.css requests to CSS Scaffold
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function parse_request() {
	    // only process requests with "?scaffold"
	    if ( isset( $_GET['scaffold'] ) ) {

			$this->render();

			exit;
	    }
	}
	
	function render( $file = '' ) {
		
		$this->load_extensions( $this->plugin_dir_path() . 'extensions' );
		$this->load_file();
		
		if ( isset( $_GET['scaffold'] ) ) {
			// Output to browser
			header('Content-type: text/css');
			echo $this->css->contents;
			exit;
		}else {
			return $this->css->contents;
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
	 * Loads the extension files
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function load_extensions( $path ) {	
		# Scaffold_Helper object
		// $helper = $this->getHelper();
		
		# The glob path
		$path = realpath($path) . DIRECTORY_SEPARATOR . '*';

		# Load each of the extensions
		foreach(glob($path) as $ext) {
			$config 	= array();
			$name 		= basename( pathinfo( $ext, PATHINFO_FILENAME ) );
			$class 		= 'StormStyles_Extension_' . $name;
			$file 		= $ext.DIRECTORY_SEPARATOR.$name.'.php';
			$include = true;
			
			if ( 'php' == pathinfo($ext, PATHINFO_EXTENSION) ) {
				include $ext;
			}else if ( file_exists($file) && is_dir($ext)) {
				require_once realpath( $file );
			}else {
				$include = false;
			}
			
			if ($include) {
				$object = new $class($config);
				// $object->attach_helper($helper);
				$this->attach($name,$object);
			}
		}
		// return $scaffold;
	}
	
	function file_path() {
		global $blog_id;
		
		$uri = $path = $cache_path = $cache_uri = false;
		
		if ( !empty($_GET['file']) ) {
			$search_paths = array(
				$_GET['file'],
				trailingslashit(ABSPATH).$_GET['file'],
			);
		}

		$search_paths[] = get_stylesheet_directory().'/css/styles-admin.css';
		$search_paths[] = get_stylesheet_directory().'/styles-admin.css';
		$search_paths[] = $this->plugin_dir_path().'themes/'.get_template().'.css';
		
		
		// Search for CSS file in order of priority and stop at the first one found
		foreach ($search_paths as $file ) {
			if ( file_exists($file) ) {
				$path = $file;
				break;
			}
		}

		if ( empty($path) ) {
			FB::error('Could not find CSS to load for Styles GUI in '.__FILE__.':'.__LINE__);
			return false;
		}
		
		// URI for enqueing
		$uri = str_replace( ABSPATH, '', $path );

		// Cache file
		$upload_dir = wp_upload_dir();
		if ( is_multisite() ) {
			$cache_file = "/styles/style-$blog_id.css";
		}else {
			$cache_file = "/styles/style.css";
		}
		
		$cache_dir = dirname($upload_dir['basedir'].$cache_file);
		if ( wp_mkdir_p( $cache_dir ) && is_writable( $cache_dir ) ) {
			$cache_path = $upload_dir['basedir'].$cache_file;
			$cache_uri = $upload_dir['baseurl'].$cache_file;
		}else {
			$cache_path = false;
		}
		
		$array = compact('uri', 'path', 'cache_path', 'cache_uri');
		
		return $array;
	}
	
	/**
	 * Initialize files object based on WordPress style queue
	 * 
	 * @since 0.1.3
	 * @return void
	 **/
	function load_file() {
		global $blog_id;

		// Find SCSS file
		$file = apply_filters( 'storm_styles_file_path', $this->file_path() );
		if ( !$file ) {
			return;
		}

		if ( is_admin() || isset($_GET['scaffold']) || 'admin-ajax.php' == basename($_SERVER['PHP_SELF']) ) {
			// Load up variables from SCSS if we're in Admin or running a cache save via AJAX
			
			if ( isset($_GET['preview']) ) {
				$this->options = get_option( 'StormStyles-preview' );
			}else {
				$this->options = get_option( 'StormStyles' );
			}

			$this->css = new StormCSSParser( $file['path'], $this );

			if ( !isset($_GET['scaffold']) ) {
				// No need to init vars if we're just outputting
				$this->file   = new StormStyles_Extension_Variable( $file, $this );

				// Merge values from database into variable objects
				if ( @is_object( $this->options['variables'] ) && is_object($this->file) ) {
					$this->file->set( $this->options['variables']->get() );
				}
			}
		}
		
		if ( false !== $this->file ) { return; }

		if ( BSM_DEVELOPMENT === true ){
			
			// Development: Force re-render on every CSS load
			wp_enqueue_style('storm-scaffold', '/?scaffold', array(), time() );
			
		}else if ( $file['cache_uri'] !== false) {
			// Enqueue cached output
			wp_enqueue_style('storm-scaffold', $file['cache_uri'] );

		}else {
			add_action( 'wp_head', array($this, wp_head_output), 999 );
		}

		// Merge values from database into variable objects
		if ( is_object( $this->options ) && is_object($this->file) ) {
			$this->file->set( $this->options->get() );
		}
	}
	
	/**
	 * Load CSS variables, extensions, and scaffold objects
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function build() {
		$this->load_file();
	}
	
	function wp_head_output() {
		$css = get_option('StormStyles-cache');
		if (!empty($css)) {
			echo "<style id='storm-scaffold-css'>$css</style>";
		}
	}
	
	function dequeue_at_imports() {
		global $wp_styles;
		
		$options = get_option( 'StormStyles' );
		$loaded = $options['loaded_imports'];
		
		foreach( $wp_styles->queue as $handle ) {
			$src = $wp_styles->registered[$handle]->src;
			
			$src_rel = str_replace( $wp_styles->content_url, '', $src );
			
			foreach ( (array)$loaded as $abspath ) {
				if ( false !== strpos( $abspath, $src_rel ) ) {
					// This file was loaded via @import. Dequeue it.
					wp_dequeue_style( $handle );
				}
			}
		}
		
	}
	
	
} // END StormStyles class

/**
 * Instantiate the StormStylesFrontend or $StormStylesController Class
 *
 * Deactivate and die if files can not be included
 */
function StormStylesInit() {
	
	if ( is_admin () ) {
	
		// Admin class
		$file = '/classes/StormStylesAdmin.php';
		if ( @include dirname ( __FILE__ ) . $file ) {
			global $StormStylesController;
			$StormStylesController = new StormStylesAdmin ();
			
		} else {
			StormStyles::deactivate_and_die ( dirname ( __FILE__ ) . $file );
		}
	} else {
		// Just the basics
		global $StormStylesController;
		$StormStylesController = new StormStyles ();
	}
}
add_action('init', 'StormStylesInit');

?>
