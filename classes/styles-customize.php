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
		
		add_action( 'customize_register', array( $this, 'add_sections' ), 5 );
		add_action( 'customize_controls_enqueue_scripts',  array( $this, 'enqueue_scripts' ) );
	
	}

	public function enqueue_scripts() {

		// Stylesheets
		wp_enqueue_style(  'styles-customize', plugins_url( '/css/styles-customize.css', STYLES_BASENAME ), array( 'wp-color-picker' ), $this->plugin->version );

		// Javascript
		wp_enqueue_script( 'styles-customize', plugins_url( '/js/styles-customize.js', STYLES_BASENAME ), array( 'wp-color-picker' ), $this->plugin->version );

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

		// Load settings from various sources with filters
		add_filter( 'styles_customize_settings', array( $this, 'load_settings_from_theme_file' ), 50 );

		// Plugin Authors: Filter to override settings sources
		$this->settings = apply_filters( 'styles_customize_settings', $this->settings );

		return $this->settings;
	}

	/**
	 * Load settings from theme file formatted as JSON
	 */
	public function load_settings_from_theme_file( $defaults ) {
		$settings = array();
		$theme_file = get_stylesheet_directory() . '/customize.json';

		if ( file_exists( $theme_file ) ) {
			$json = file_get_contents( $theme_file );
			$settings = json_decode( $json, true );
		}

		return wp_parse_args( $settings, $defaults );
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

		foreach ( $elements as $element ) {
			if ( $class = Styles_Helpers::get_element_class( $element ) ) {
				$class::add_item( $group_id, $element );
			}
		}

	}

}