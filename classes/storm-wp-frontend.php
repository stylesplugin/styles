<?php

class Storm_WP_Frontend {
	
	/**
	 * Full file system path to the main plugin file
	 * 
	 * @var string
	 */
	var $plugin_file;

	/**
	 * Path to the main plugin file relative to WP_CONTENT_DIR/plugins
	 * 
	 * @var string
	 */
	var $plugin_basename;
	
	/**
	 * Path to CSS file being manipulated
	 * 
	 * @var string
	 **/
	var $file = false;
	
	/**
	 * Reference back to the parent StormStyles object
	 * 
	 * @var StormStyles
	 **/
	var $styles;

	function __construct( $styles ) {
		// Pointer to parent object
		$this->styles = $styles;
		
		// Full path and plugin basename of the main plugin file
		$this->plugin_file = __FILE__;
		$this->plugin_basename = plugin_basename( $this->plugin_file );
		
		$this->register_scripts();

		// AJAX hooks so that we can access WordPress data when requesting processed CSS
		add_action('parse_request', array( $this, 'parse_request') );
		add_filter('query_vars', array( $this, 'query_vars') );
		
		// Frontend Javascript
		add_action ( 'template_redirect' , array( $this , 'frontend_js' ) );
		
		// Admin Bar
		add_action( 'admin_bar_menu', array( $this, 'admin_bar' ), 95 );

		// If we loaded CSS through @import, tell WordPress to dequeue
		// add_action( 'wp_print_styles', array( $this , 'dequeue_at_imports' ), 0);

	}
	
	function admin_bar() {
		global $wp_admin_bar;
		if ( current_user_can('manage_options') && is_admin_bar_showing() ) {
			$wp_admin_bar->add_menu( array( 'parent' => 'appearance', 'id' => 'storm-styles', 'title' => __( 'Styles' ), 'href' => admin_url('themes.php?page=styles'), ) );
		}
		
	}
	
	function register_scripts() {
		wp_register_script('storm-colorpicker'    , $this->plugin_url().'/js/colorpicker/js/colorpicker.js',array('jquery'), $this->version, true);
		wp_register_script('storm-jq-ui-slider'   , $this->plugin_url().'/js/jquery.ui.slider.min.js'      ,array('jquery', 'jquery-ui-core' ), $this->version, true);
		wp_register_script('storm-gradient-picker', $this->plugin_url().'/js/jq.gradientpicker.js'         ,array('storm-jq-ui-slider', 'storm-colorpicker' ), $this->version, true);
		wp_register_script('jqcookie'             , $this->plugin_url().'/js/jquery.cookie.js'             ,array('jquery'), $this->version, true);

		wp_register_script('storm-admin-main'     , $this->plugin_url().'/js/admin.js'                     ,array('jqcookie', 'storm-gradient-picker', 'storm-jq-ui-slider', 'storm-colorpicker', 'thickbox', 'media-upload' ), $this->version, true);
	}
	
	/**
	 * Enqueue javascript required for the front-end editor
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
	 * @return mixed
	 **/
	function get_option($option) {
		if ( isset( $this->options[$option] ) ) {
			return $this->options[$option];
		}else if ( isset( $this->api_options[$option] ) ) {
			return $this->api_options[$option];
		}else {
			return false;
		}
	}
	
	/**
	 * Get the full URL to the plugin
	 *
	 * @author Matt Martz <matt@sivel.net>
	 * @return string
	 */
	function plugin_url() {
		$plugin_url = plugins_url ( basename( dirname( dirname( __FILE__) ) ) );
		return $plugin_url;
	}
	
	/**
	 * Get the full path to the plugin (with trailing slash)
	 * 
	 * @return string
	 **/
	function plugin_dir_path() {
		return plugin_dir_path( dirname(__FILE__) );
	}
	
	/**
	 * Strip ABSPATH from a path.
	 * 
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
	 * @return none
	 */
	function deactivate_and_die( $file ) {
		load_plugin_textdomain ( 'styles' , false , 'StormStyles/localization' );
		$message = sprintf ( __( "Styles has been automatically deactivated because the file <strong>%s</strong> is missing. Please reinstall the plugin and reactivate." ) , $file );
		if ( ! function_exists ( 'deactivate_plugins' ) ) {
			include ( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		deactivate_plugins ( __FILE__ );
		wp_die ( $message );
	}
	
	/**
	 * Load and output a view file. Pass it some data somehow.
	 * 
	 * @return void
	 **/
	function load_view($view) {
		
		$file = $this->plugin_dir_path().'/views/'.$view;
		
		if ( ! @include ( $file ) ) {
			_e ( sprintf ( '<div id="message" class="updated fade"><p>The file <strong>%s</strong> is missing.  Please reinstall the plugin.</p></div>' , $file ), 'styles' );
		}
	}
	
	/**
	 * Pass ?scaffold requests to the CSS processor
	 * 
	 * @return void
	 **/
	function parse_request() {
	    // only process requests with "?scaffold"
	    if ( isset( $_GET['scaffold'] ) ) {

			do_action('styles_init', $this->styles );
			do_action('styles_before_process', $this->styles);
			do_action('styles_process', $this->styles);
			do_action('styles_after_process', $this->styles);
			do_action('styles_render', $this->styles );

			exit;
	    }
	}
	
	/**
	 * Whitelist the scaffold query var for parse_request()
	 * 
	 * @return void
	 **/
	function query_vars($vars) {
	    $vars[] = 'scaffold';
	    return $vars;
	}
	
}