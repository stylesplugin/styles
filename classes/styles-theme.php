<?php

class Styles_Theme {

	/**
	 * @var Styles_Plugin
	 */
	var $plugin;

	/**
	 * @var array
	 */
	var $settings = array();

	var $theme_plugins = array();

	function __construct( $plugin ) {
		$this->plugin = $plugin;

		// Plugins for themes add 'styles-themeslug' to this array
		$this->theme_plugins = apply_filters( 'styles_theme_plugins', array() );

		// Load settings from various sources with filters
		add_filter( 'styles_theme_settings', array( $this, 'load_theme_settings_from_plugin' ), 1 );
		add_filter( 'styles_theme_settings', array( $this, 'load_settings_from_theme' ), 5 );
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
		$this->settings = apply_filters( 'styles_theme_settings', $this->settings );

		return $this->settings;
	}

	/**
	 * Load settings from path provided by plugin
	 */
	public function load_theme_settings_from_plugin( $defaults = array() ) {
		$plugin_dir = 'styles-' . get_template();
		if ( in_array( $plugin_dir , $this->theme_plugins ) ) {
			$json_file = WP_PLUGIN_DIR . '/' . $plugin_dir . '/customize.json';
			return $this->load_settings_from_json_file( $json_file, $defaults );
		}
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

}