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
		$this->plugin_basename = plugin_basename( $this->get_plugin_directory() . '/plugin.php' );

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

	/**
	 * Used in favor of plugin_dir_path() because plugin might be installed in a theme.
	 *
	 * @return string Path to this plugin's directory.
	 */
	public function get_plugin_directory() {
		if ( isset( $this->plugin_directory ) ) {
			return $this->plugin_directory;
		}

		$this->plugin_directory = realpath( dirname( dirname( __FILE__ ) ) );
		// Sanitize for Win32 installs
		$this->plugin_directory = str_replace( array( '/', '\\' ), '/', $this->plugin_directory );
		$this->plugin_directory = preg_replace('|/+|', '/', $this->plugin_directory );

		return $this->plugin_directory;
	}

	/**
	 * Used in favor of plugin_dir_path() because plugin might be installed in a theme.
	 *
	 * @return string URL to this plugin's directory
	 */
	public function get_plugin_url() {
		if ( isset( $this->plugin_url ) ) {
			return $this->plugin_url;
		}

		$template_directory   = realpath( get_template_directory() );
		$stylesheet_directory = realpath( get_stylesheet_directory() );

		if ( false !== strpos( $this->get_plugin_directory(), $template_directory ) ) {

			// Parent theme
			$path_in_theme = str_replace( $template_directory, '', $this->get_plugin_directory() );
			$this->plugin_url = get_template_directory_uri() . $path_in_theme;

		}else if ( false !== strpos( $this->get_plugin_directory(), $stylesheet_directory ) ) {

			// Child theme
			$path_in_theme = str_replace( $stylesheet_directory, '', $this->get_plugin_directory() );
			$this->plugin_url = get_stylesheet_directory_uri() . $path_in_theme;

		}else {

			// Plugins or mu-plugins
			$this->plugin_url = plugins_url( '', $this->get_plugin_directory() . '/plugin.php' );

		}

		return $this->plugin_url;
	}

	public function print_scripts() {
		if ( $this->scripts_printed ) { return false; }

		wp_register_script( 'styles-chosen', $this->get_plugin_url() . '/js/chosen/chosen.jquery.min.js', array( 'jquery' ), $this->version );
		wp_register_script( 'styles-font-menu', $this->get_plugin_url() . '/js/styles-font-menu.js', array( 'jquery', 'styles-chosen' ), $this->version );
		wp_register_style( 'styles-chosen', $this->get_plugin_url() . '/js/chosen/chosen.css', array(), $this->version );
		wp_register_style( 'styles-font-menu', $this->get_plugin_url() . '/css/styles-font-menu.css', array(), $this->version );
		// wp_register_style( 'styles-chosen', $this->get_plugin_url() . '/js/chosen/chosen.min.css', array(), $this->version );

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