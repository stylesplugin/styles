<?php

require_once dirname(__FILE__) . '/sfm-admin.php';
require_once dirname(__FILE__) . '/sfm-group.php';
require_once dirname(__FILE__) . '/sfm-group-standard.php';
require_once dirname(__FILE__) . '/sfm-group-google.php';
require_once dirname(__FILE__) . '/sfm-single-standard.php';
require_once dirname(__FILE__) . '/sfm-single-google.php';
require_once dirname(__FILE__) . '/sfm-image-preview.php';

/**
 * Controller class
 * Holds instances of models in vars
 * Loads views from views/ directory
 * 
 * Follows the Singleton pattern. @see http://jumping-duck.com/tutorial/wordpress-plugin-structure/
 * @example Access plugin instance with $font_dropdown = SFM_Plugin::get_instance();
 */
class SFM_Plugin {

	/**
	 * @var string The plugin version.
	 */
	var $version = '1.0.1';

	/**
	 * @var Styles_Font_Menu Instance of the class.
	 */
	protected static $instance = false;

	/**
	 * @var string Class to apply to menu element and prefix to selectors.
	 */
	public $menu_class = 'sfm';

	/**
	 * @var SFM_Admin Methods for WordPress admin user interface.
	 */
	var $admin;

	/**
	 * @var SFM_Group_Standard Web standard font families and CSS font stacks.
	 */
	var $standard_fonts;

	/**
	 * @var SFM_Group_Google Connects to Google Font API.
	 */
	var $google_fonts;

	/**
	 * @var SFM_Image_Preview Generate image preview of a font.
	 */
	var $image_preview;

	/**
	 * Set with site_url() because we might not be running as a plugin.
	 * 
	 * @var string URL for the styles-font-menu directory.
	 */
	var $plugin_url;

	/**
	 * Set with dirname(__FILE__) because we might not be running as a plugin.
	 * 
	 * @var string Path for the styles-font-menu directory.
	 */
	var $plugin_directory;

	/**
	 * Intentionally inaccurate if we're running as a plugin.
	 * 
	 * @var string Plugin basename, only if we're running as a plugin.
	 */
	var $plugin_basename;

	/**
	 * print_scripts() runs as late as possible to avoid processing Google Fonts.
	 * This prevents running multiple times.
	 * 
	 * @var bool Whether we have already registered scripts or not.
	 */
	var $scripts_printed = false;

	/**
	 * Don't use this. Use ::get_instance() instead.
	 */
	public function __construct() {
		if ( !self::$instance ) {
			$message = '<code>' . __CLASS__ . '</code> is a singleton.<br/> Please get an instantiate it with <code>' . __CLASS__ . '::get_instance();</code>';
			wp_die( $message );
		}
	}

	public static function get_instance() {
		if ( !is_a( self::$instance, __CLASS__ ) ) {
			self::$instance = true;
			self::$instance = new self();
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	 * Initial setup. Called by get_instance.
	 */
	protected function init() {
		// Fix for IIS paths
		$normalized_abspath = str_replace(array('/', '\\'), '/', ABSPATH );

		$this->plugin_directory = str_replace(array('/', '\\'), '/', dirname( dirname( __FILE__ ) ) );
		$this->plugin_url = site_url( str_replace( $normalized_abspath, '', $this->plugin_directory ) );
		$this->plugin_basename = plugin_basename( $this->plugin_directory . '/plugin.php' );

		$this->admin = new SFM_Admin( $this );
		$this->google_fonts = new SFM_Group_Google();
		$this->standard_fonts = new SFM_Group_Standard();
		$this->image_preview = new SFM_Image_Preview();

		/**
		 * Output dropdown menu anywhere styles_font_menu action is called.
		 * @example <code>do_action( 'styles_font_menu' );</code>
		 */
		add_action( 'styles_font_menu', array( $this, 'get_view_menu' ), 10, 2 );
	}

	public function print_scripts() {
		if ( $this->scripts_printed ) { return false; }

		wp_register_script( 'styles-chosen', $this->plugin_url . '/js/chosen/chosen.jquery.min.js', array( 'jquery' ), $this->version );
		wp_register_script( 'styles-font-menu', $this->plugin_url . '/js/styles-font-menu.js', array( 'jquery', 'styles-chosen' ), $this->version );
		wp_register_style( 'styles-chosen', $this->plugin_url . '/js/chosen/chosen.css', array(), $this->version );
		wp_register_style( 'styles-font-menu', $this->plugin_url . '/css/styles-font-menu.css', array(), $this->version );
		// wp_register_style( 'styles-chosen', $this->plugin_url . '/js/chosen/chosen.min.css', array(), $this->version );

		// Pass Google Font Families to javascript
		// This saves on bandwidth by outputing them once,
		// then appending them to all <select> elements client-side
		wp_localize_script( 'styles-font-menu', 'styles_standard_fonts', $this->standard_fonts->option_values );
		wp_localize_script( 'styles-font-menu', 'styles_google_options', $this->google_fonts->option_values );

		// Output scripts and dependencies
		// Tracks whether dependencies have already been output
		wp_print_scripts( array( 'styles-font-menu' ) );
		wp_print_styles( array( 'styles-chosen' ) );
		wp_print_styles( array( 'styles-font-menu' ) );

		// Generated scripts for font previews
		echo '<style>' . $this->standard_fonts->get_menu_css() . '</style>';

		$this->scripts_printed = true;
	}

	/**
	 * Display views/menu.php
	 */
	public function get_view_menu( $attributes = '', $value = false ) {
		$args = compact( 'attributes', 'value' );
		$this->get_view( 'menu', $args );
	}

	/**
	 * Display any view from the views/ directory.
	 * Allows views to have access to $this
	 */
	public function get_view( $file = 'menu', $args = array() ) {
		extract( $args );
		$file = dirname( dirname( __FILE__ ) ) . "/views/$file.php";
		if ( file_exists( $file ) ) {
			include $file;
		}
	}
}