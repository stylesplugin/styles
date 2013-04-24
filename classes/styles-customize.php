<?php

class Styles_Customize {

	/**
	 * @var Styles_Plugin
	 */
	var $plugin;

	/**
	 * @var array
	 */
	var $settings = array();

	function __construct( $plugin ) {
		$this->plugin = $plugin;
		
		add_action( 'customize_register', array( $this, 'add_sections' ), 10 );
		add_action( 'customize_controls_enqueue_scripts',  array( $this, 'enqueue_scripts' ) );
	
		// Load settings from various sources with filters
		add_filter( 'styles_customize_settings', array( $this, 'load_settings_from_plugin' ), 20 );
		add_filter( 'styles_customize_settings', array( $this, 'load_settings_from_theme' ), 50 );

		// Set storm-styles option to not autoload; does nothing if setting already exists
		add_option( Styles_Helpers::get_option_key(), '', '', 'no' );

	}

	public function enqueue_scripts() {

		// Stylesheets
		wp_enqueue_style(  'styles-customize', plugins_url( '/css/styles-customize.css', STYLES_BASENAME ), array(), $this->plugin->version );

		// Javascript
		wp_enqueue_script( 'styles-customize', plugins_url( '/js/styles-customize.js', STYLES_BASENAME ), array(), $this->plugin->version );

	}

	/**
	 * Load settings as JSON either from transient / API or theme file
	 *
	 * @return array
	 */
	public function get_settings() {

		// Return cached settings if they've already been processed
		if ( !empty( $this->settings ) ) {
			return $this->settings;
		}

		// Plugin Authors: Filter to override settings sources
		$this->settings = apply_filters( 'styles_customize_settings', $this->settings );

		return $this->settings;
	}

	/**
	 * Load settings from path provided by plugin
	 */
	public function load_settings_from_plugin( $defaults = array() ) {
		$json_file = apply_filters( 'styles_customize_json_file', null );
		return $this->load_settings_from_json_file( $json_file, $defaults );
	}

	/**
	 * Load settings from theme file formatted as JSON
	 */
	public function load_settings_from_theme( $defaults = array() ) {
		$json_file = get_stylesheet_directory() . '/customize.json';
		return $this->load_settings_from_json_file( $json_file, $defaults );
	}

	public function load_settings_from_json_file( $json_file, $default_settings = array() ) {
		$settings = array();
		if ( file_exists( $json_file ) ) {
			$json =  preg_replace('!/\*.*?\*/!s', '', file_get_contents( $json_file ) ); // strip comments before decoding
			$settings = json_decode( $json, true );

			if ( $json_error = Styles_Helpers::get_json_error( $json_file, $settings ) ) {
				wp_die( $json_error );
			}
		}
		return wp_parse_args( $settings, $default_settings );
	}

	/**
	 * Register sections with WordPress theme customizer in WordPress 3.4+
	 * e.g., General, Header, Footer, Content, Sidebar
	 */
	function add_sections( $wp_customize ) {
		global $wp_customize;

		$i = 950;
		foreach ( $this->get_settings() as $group => $elements ) {
			$i++;
			
			// Groups
			$group_id = Styles_Helpers::get_group_id( $group );
			$wp_customize->add_section( $group_id, array(
				'title'    => __( $group, 'storm' ),
				'priority' => $i,
			) );

			self::add_items( $group_id, $elements );
		}
	}


	/**
	 * Register individual customize fields in WordPress 3.4+
	 * Settings & Controls are within each class (type calls different classes)
	 */
	public function add_items( $group_id, $elements ) {
		static $i;
		foreach ( $elements as $element ) {
			$i++;
			$element['priority'] = $i;
			if ( $class = Styles_Helpers::get_element_class( $element ) ) {

				// PHP <= 5.2 support
				// Otherwise, would be: $class::add_item( $group_id, $element );
				call_user_func_array( $class.'::add_item', array( $group_id, $element ) );
			}
		}

	}

}