<?php

/**
 * Plugin wrapper
 **/
class Styles_Plugin {

	/**
	 * @var Styles_Plugin Instance of this class.
	 */
	private static $instance = false;
	
	/**
	 * Plugin Version
	 *
	 * Holds the current plugin version.
	 *
	 * @var string
	 **/
	var $version = '1.0.18';

	/**
	 * @var Styles_CSS
	 */
	var $css;

	/**
	 * @var Styles_Customize
	 */
	var $customize;

	/**
	 * @var Styles_Admin
	 */
	var $admin;

	/**
	 * @var Styles_Upgrade
	 */
	var $upgrade;

	/**
	 * @var Styles_Child
	 */
	var $child;

	/**
	 * Class added to body and all selectors
	 *
	 * @var string
	 */
	var $body_class = 'styles';

	/**
	 * Don't use this. Use ::get_instance() instead.
	 */
	public function __construct() {
		if ( !self::$instance ) {
			$message = '<code>' . __CLASS__ . '</code> is a singleton.<br/> Please get an instantiate it with <code>' . __CLASS__ . '::get_instance();</code>';
			wp_die( $message );
		}       
	}

	/**
	 * Maybe instantiate, then return instance of this class.
	 * @return Styles_Plugin Controller instance.
	 */
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

		require_once dirname( __FILE__ ) . '/styles-helpers.php';

		add_action( 'wp_head', array( $this, 'wp_head' ), 1000 );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 15 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 1 );
		add_action( 'customize_register', array( $this, 'customize_register' ), 1 );
		add_action( 'customize_save_after', array( $this, 'customize_save_after' ) );

	}

	/**
	 * Set up detection for child plugins that follow common patterns
	 */
	public function plugins_loaded() {
		if ( ( !is_a( $this->child, 'Styles_Child') && is_user_logged_in() )
			|| apply_filters( 'styles_force_rebuild', false )
		) {

			// Only for logged-in users.
			// Targeting theme preview and customize.php.
			$this->increase_memory_limit();

			require_once dirname( __FILE__ ) . '/styles-child.php';
			require_once dirname( __FILE__ ) . '/styles-child-updatable.php';
			require_once dirname( __FILE__ ) . '/styles-child-theme.php';

			$this->child = new Styles_Child( $this );
		}
	}

	/**
	 * Setup WP Admin user interface
	 */
	public function admin_menu() {
		if ( !is_a( $this->admin, 'Styles_Admin') ) {

			require_once dirname( __FILE__ ) . '/styles-admin.php';
			require_once dirname( __FILE__ ) . '/styles-upgrade.php';

			$this->admin = new Styles_Admin( $this );

			$this->upgrade = new Styles_Upgrade();
			add_action( 'admin_init', array( $this->upgrade, 'maybe_upgrade' ) );
		}
	}

	/**
	 * Add settings to WP Customize
	 */
	public function customize_register( $wp_customize = null ) {
		if ( !is_a( $this->customize, 'Styles_Customize') ) {

			require_once dirname( __FILE__ ) . '/styles-control.php';
			require_once dirname( __FILE__ ) . '/styles-customize.php';

			$this->customize = new Styles_Customize( $this );
		}
	}

	/**
	 * Rebuild CSS whenever customizer is re-saved
	 */
	function customize_save_after() {
		$this->init_css();
		$this->css->get_css();
	}

	public function init_css() {
		if ( !is_a( $this->css, 'Styles_CSS') ) {

			require_once dirname( __FILE__ ) . '/styles-control.php';
			require_once dirname( __FILE__ ) . '/styles-css.php';

			$this->css = new Styles_CSS( $this );
		}
	}

	public function get_css() {
		global $wp_customize;

		$css = false;

		if ( empty( $wp_customize ) ) {
			$css = get_option( Styles_Helpers::get_option_key( 'css' ) );
		}

		if ( !empty( $wp_customize ) || empty( $css ) || apply_filters( 'styles_force_rebuild', false ) ) {
			// Rebuild
			$this->init_css();
			return $this->css->get_css();
		}else {
			return $css;
		}
	}

	/**
	 * Output <style> tag in head.
	 *
	 * For dynamic CSS, this is much faster than using a <link> tag, which would reload WordPress.
	 * @see http://stylesplugin.com/our-first-review
	 */
	public function wp_head() {
		echo implode( PHP_EOL, array(
			'',
			'<!-- Styles cached and displayed inline for speed. Generated by http://stylesplugin.com -->',
			'<style type="text/css" id="styles-plugin-css">',
			$this->get_css(),
			'</style>',
			'',
		));
	}

	/**
	 * Add Styles body_class to <body> tag
	 */
	public function body_class( $classes ) {
		$classes[] = $this->body_class;
		return $classes;
	}

	public function get_option( $key = 'version' ) {
		$options = get_option( 'storm-styles' );
		if ( isset( $options[ $key ] ) ) {
			return $options[ $key ];
		}else {
			return false;
		}
	}

	public function set_option( $key, $value ) {
		$options = get_option( 'storm-styles' );

		$options[ $key ] = $value;

		update_option( 'storm-styles', $options );
	}

	/**
	 * Load HTML template from templates directory.
	 * Contents of $args are turned into variables for use in the template.
	 * 
	 * For example, $args = array( 'foo' => 'bar' );
	 *   becomes variable $foo with value 'bar'
	 */
	public static function get_view( $file, $args = array() ) {
		extract( $args );

		$file = dirname( dirname( __FILE__ ) ) . "/views/$file.php";

		if ( file_exists( $file ) ) {
			require $file;
		}
	}

	/**
	 * Increase memory limit; for logged-in users only.
	 * Not the same as increasing memory *usage*.
	 * Gives extra padding to customize.php and its preview.
	 * 
	 * Based on pluginbuddy.php, a part of iTheme's BackupBuddy.
	 * 
	 * @author Dustin Bolton
	 */
	public static function increase_memory_limit()  {
		// Increase the memory limit
		$current_memory_limit = trim( @ini_get( 'memory_limit' ) );
		
		// Make sure a minimum memory limit of 256MB is set.
		if ( preg_match( '/(\d+)(\w*)/', $current_memory_limit, $matches ) ) {
			$current_memory_limit = $matches[1];
			$unit = $matches[2];
			// Up memory limit if currently lower than 256M.
			if ( 'g' !== strtolower( $unit ) ) {
				if ( ( $current_memory_limit < 256 ) || ( 'm' !== strtolower( $unit ) ) )
					@ini_set('memory_limit', '256M');
			}
		} else {
			// Couldn't determine current limit, set to 256M to be safe.
			@ini_set('memory_limit', '256M');
		}
	}

}