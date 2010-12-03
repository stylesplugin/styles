<?php
/*
Plugin Name: PD Styles
Plugin URI: http://pdclark.com
Description: Rewrite.
Version: 0.1.2
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
	include  dirname ( __FILE__ ) . '/lib/extensions/FirePHPCore/fb.php';
}


include dirname ( __FILE__ ) . '/lib/extensions/Observable.php';
include dirname ( __FILE__ ) . '/lib/extensions/Observer.php';
include dirname ( __FILE__ ) . '/lib/extensions/PDStylesObserver.php';
include dirname ( __FILE__ ) . '/lib/extensions/File/File.php';
include dirname ( __FILE__ ) . '/lib/extensions/Variable/Variable.php';
include dirname ( __FILE__ ) . '/lib/extensions/Group/Group.php';


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
	var $version = '0.1.3';
	
	/**
	 * Plugin DB version
	 * 
	 * Holds the current plugin database version. 
	 * Not the same as the current plugin version.
	 * 
	 * @var int
	 **/
	var $db_version = '0.1.1';
	
	/**
	 * Options array containing all options for this plugin
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $options;
	
	/**
	 * Objet containing references to scaffold files
	 * 
	 * @since 0.1.3
	 * @var PDStyles_Extension_File
	 **/
	var $files;
	
	/**
	 * Path to CSS file being manipulated
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $file;
	
	/**
	 * Friendly formatting of path to $this->file from WP basedir
	 * Allows for CSS links to not break between dev & production environments.
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $permalink;
	
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
		
		// search for and process enqueued SCSS files
		add_action( 'wp_print_styles', array( &$this , 'build' ), 9999);
		add_action( 'admin_print_styles', array( &$this , 'build' ), 0);

		$this->register_scripts();
		$this->load_extensions( $this->plugin_dir_path() . 'extensions' );

	}
	
	/**
	 * Register scripts for use in front or back end
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function register_scripts() {
		wp_deregister_script('jquery');//deregister current jquery
		wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js', false, '1.4.4', true);
		wp_enqueue_script('jquery');
		
		wp_register_script('pds-colorpicker', $this->plugin_url().'/lib/js/colorpicker/js/colorpicker.js',array('jquery'), $this->version, true);
		
		wp_register_script('jqcookie', $this->plugin_url().'/lib/js/jquery.cookie.js',array(), $this->version, true);
		
		
		// Not normally registered in frontend
		wp_register_script('pds-media-upload', admin_url('js/media-upload.js'), array( 'thickbox' ));
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
			
			$this->load_files();

			// Get file path from SCSS active_id
			if ( empty( $_GET['file'] ) && isset( $_GET['active_id'] ) ) {
				$_GET['file'] = $this->files->queue[ $_GET['active_id'] ]->file;
			}

			$this->options = get_option( 'pd-styles' );

			$config = $this->get_scaffold_config();
			
			if ( isset( $_GET['preview'] ) ) {
				$config['PDStyles']['preview'] = true;
			}
			
			// From scaffold/index.php
			/**
			 * The location of this system folder
			 */
			$system = $this->plugin_dir_path() . 'scaffold'; // No trailing slash
			
			/**
			 * The environment class helps us handle errors
			 * and autoloading of classes. It's not required
			 * to make Scaffold function, but makes it a bit
			 * nicer to use.
			 */
			require_once $system.'/lib/Scaffold/Environment.php';

			/**
			 * Set timezone, just in case it isn't set. PHP 5.3+ 
			 * throws a tantrum if you try and use time() without
			 * this being set.
			 */
			date_default_timezone_set('GMT');

			/**
			 * Automatically load any Scaffold Classes
			 */
			Scaffold_Environment::auto_load();

			/**
			 * Let Scaffold handle errors
			 */
			Scaffold_Environment::handle_errors();

			/** 
			 * Set the view to use for errors and exceptions
			 */
			Scaffold_Environment::set_view(realpath($system.'/views/error.php'));

			// =========================================
			// = Start the scaffolding magic  =
			// =========================================

			// The container creates Scaffold objects
			$Container = new Scaffold_Container($system,$config);

			// This is where the magic happens
			$Scaffold = $Container->build();

			// Get the sources
			$Source = $Scaffold->getSource(null,$config);

			// Compiles the source object
			$Source = $Scaffold->compile($Source);

			// Use the result to render it to the browser. Hooray!
			$Scaffold->render($Source);

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
		$config		= array(
			'production'			=> false,
			'max_age'				=> false,
			'output_compression'	=> false,
			'set_etag'				=> true,
			'enable_string'			=> false,
			'enable_url'			=> false,
			'extensions'			=> array(
				// 'AbsoluteUrls',
				'Embed',
				'Functions',
				//'HSL',
				'ImageReplace',
				// 'Minify',
				'Properties',
				'Random',
				'Import',
				'Mixins',
				'NestedSelectors',
				//'XMLVariables',
				'Variables',
				'PDStyles',
				'Gradient',
        	
				# Process-heavy Extensions
				//'Sass',
				//'CSSTidy',
				//'YUI'
			),
		);
		
		$config['load_paths'] = array(
			untrailingslashit( get_stylesheet_directory() ),
			untrailingslashit( $this->plugin_dir_path() ),
		);
		
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
	public function load_extensions( $path ) {	
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
	
	/**
	 * Initialize files object based on WordPress style queue
	 * 
	 * @since 0.1.3
	 * @return void
	 **/
	function load_files() {
		if ( is_a( $this->files, 'PDStyles_Extension_File' ) ) { return; }
		
		$this->files = new PDStyles_Extension_File();
		
		// Setup CSS path
		$this->permalink = $this->files->active_id;
		$this->file = $this->files->queue[ $this->permalink ]->file;
		
		// Merge values from database into variable objects
		if ( is_object( $this->options['variables'][ $this->permalink ] ) ) {
			$this->files->active_file->set( array( $this->permalink => $this->options['variables'][ $this->permalink ]->get() ) );
		}
	}
	
	/**
	 * Load CSS variables, extensions, and scaffold objects
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function build() {
		$this->load_files();
	}
	
} // END PDStyles class
/**
 * Instantiate the PDStylesFrontend or $PDStylesAdminController Class
 *
 * Deactivate and die if files can not be included
 */
function PDStylesInit() {
	
	if ( is_admin () ) {
	
		// include admin class

		if ( @include dirname ( __FILE__ ) . '/lib/controllers/PDStylesAdminController.php' ) {
			global $PDStylesAdminController;
			$PDStylesAdminController = new PDStylesAdminController ();

		} else {
			PDStyles::deactivate_and_die ( dirname ( __FILE__ ) . '/inc/admin.php' );
		}
	} else {
		
		// include subadmin class
	
		if ( @include dirname ( __FILE__ ) . '/lib/controllers/PDStylesFrontendController.php' ) {
			global $PDStylesFrontendController;
			$PDStylesFrontendController = new PDStylesFrontendController ();
		} else {
			PDStyles::deactivate_and_die ( dirname ( __FILE__ ) . '/inc/front-end.php' );
		}
	}
}
add_action('init', 'PDStylesInit');




?>