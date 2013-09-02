<?php

class Styles_Child_Theme extends Styles_Child_Updatable {

	var $template;
	var $styles_css;

	public function __construct( $args ) {
		parent::__construct( $args );

		$this->template = str_replace( ' ', '-', strtolower( $this->item_name ) );
		$this->styles_css = dirname( $this->plugin_file ) . '/style.css';
		$this->plugin_theme_name = trim( str_replace( 'Styles:', '', $this->name ) );

		$this->theme = wp_get_theme();

		add_filter( 'styles_css_output', array( $this, 'styles_css_output' ) );
	}

	public function is_target_parent_or_child_theme_active() {
		return ( $this->is_target_theme_active() || $this->is_target_parent_theme_active() );
	}

	public function is_target_parent_theme_active() {
		if ( !is_a( $this->theme->parent() , 'WP_Theme') ) { return false; }

		// Do parent or child theme header name == Styles plugin "Style Item" header?
		// WARNING: Don't use this option. It's likely to change.
		if ( $this->theme_name_equals_plugin_item_name( $this->theme->parent() ) ) { return true; }

		// Do parent or child theme header name == Styles plugin header name?
		if ( $this->theme_name_equals_plugin_name( $this->theme->parent() ) ) { return true; }

		// Does the parent or child directory name == Styles directory name?
		if ( $this->theme_directory_name_equals_plugin_directory_name( $this->theme->parent() ) ) { return true; }

		return false;
	}

	public function is_target_theme_active() {
		// Do parent or child theme header name == Styles plugin "Style Item" header?
		// WARNING: Don't use this option. It's likely to change.
		if ( $this->theme_name_equals_plugin_item_name( $this->theme ) ) { return true; }

		// Do parent or child theme header name == Styles plugin header name?
		if ( $this->theme_name_equals_plugin_name( $this->theme ) ) { return true; }

		// Does the parent or child directory name == Styles directory name?
		if ( $this->theme_directory_name_equals_plugin_directory_name( $this->theme ) ) { return true; }

		return false;
	}

	/**
	 * Do parent or child theme header name == Styles plugin "Style Item" header?
	 * (Case insensitive)
	 *
	 * For example:
	 *     Theme Name: Some Parent or Child Theme
	 *     Styles Item: Some Parent or Child Theme
	 *
	 * This is an override for **weird edge cases** where the Styles plugin name
	 * or folder name can't match the theme name or folder name.
	 *
	 * Warning: Don't use this. It's likely to change.
	 */
	public function theme_name_equals_plugin_item_name( $theme ) {
		if ( !is_a( $theme, 'WP_Theme') ) { return false; }

		// Strip spacing and special characters in theme names
		// Allows "Twenty Twelve" to match "TwentyTwelve"
		$santatized_item_name = $this->sanatize_name( $this->item_name );
		$santatized_theme_name  = $this->sanatize_name( $theme->get('Name') );

		if ( 0 === strcasecmp( $santatized_item_name, $santatized_theme_name ) ) { return true; }
		return false;
	}

	/**
	 * Do parent or child theme header name == Styles plugin header name?
	 * (Case insensitive)
	 *
	 * For example:
	 *     Theme Name: Some Parent or Child Theme
	 *     Plugin Name: Styles: Some Parent or Child Theme
	 *
	 * ...would return true. 
	 *
	 * "Theme Name" is in the theme header.
	 * "Plugin Name" is in the Styles plugin header.
	 */
	public function theme_name_equals_plugin_name( $theme ) {
		if ( !is_a( $theme, 'WP_Theme') ) { return false; }

		// Strip spacing and special characters in theme names
		// Allows "Twenty Twelve" to match "TwentyTwelve"
		$santatized_plugin_name = $this->sanatize_name( $this->plugin_theme_name );
		$santatized_theme_name  = $this->sanatize_name( $theme->get('Name') );

		if ( 0 === strcasecmp( $santatized_plugin_name, $santatized_theme_name ) ) { return true; }
		return false;
	}

	/**
	 * Does the parent or child directory name == Styles directory name?
	 * (Case insensitive)
	 *
	 * For example:
	 *     Theme directory: some-parent-or-child-theme
	 *     Plugin directory: styles-some-parent-or-child-theme
	 */
	public function theme_directory_name_equals_plugin_directory_name( $theme ) {
		if ( !is_a( $theme, 'WP_Theme') ) { return false; }
		if ( 0 === strcasecmp( $this->get_plugin_directory_name(), $theme->stylesheet ) ) { return true; }
		return false;
	}

	public function get_plugin_directory_name() {
		if ( isset( $this->plugin_directory_name ) ) {
			return $this->plugin_directory_name;
		}
		$plugin_directory_name = basename( dirname( $this->plugin_file ) );

		// Strip 'styles-' from the plugin directory name
		$remove = 'styles-';
		if ( $remove == strtolower( substr($plugin_directory_name, 0, strlen( $remove ) ) ) ) {
			$plugin_directory_name = substr($plugin_directory_name, strlen( $remove ) );
		}

		$this->plugin_directory_name = $plugin_directory_name;
		return $this->plugin_directory_name;
	}

	public function sanatize_name( $name ) {
		return preg_replace( '/[^a-zA-Z0-9]/', '', $name );
	}

	public function get_json_path() {
		if ( $this->is_target_parent_or_child_theme_active() ) {
			$json_file = dirname( $this->plugin_file ) . '/customize.json';
			return $json_file;
		}else {
			return false;
		}
	}

	/**
	 * If styles.css exists in the plugin folder, prepend it to final CSS output
	 */
	public function styles_css_output( $css ) {
		if ( $this->is_target_parent_or_child_theme_active() && file_exists( $this->styles_css ) ) {
			$css = file_get_contents( $this->styles_css ) . $css;
		}

		return $css;
	}

}