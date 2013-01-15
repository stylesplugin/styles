<?php

class Styles_Customizer {

	static public $settings;

	/**
	 * Load settings as JSON either from transient / API or theme file
	 */
	static public function get_settings() {

		if ( !empty( self::$settings ) ) {
			return self::$settings;
		}

		$theme_file = trailingslashit( get_stylesheet_directory() ). 'customize.json';
		$json = '{}';

		// From file
		if ( file_exists( $theme_file ) ) {
			$json = file_get_contents( $theme_file );
		}

		self::$settings = json_decode( $json, true );

		return self::$settings;
	}

	

	/**
	 * Register sections with WordPress theme customizer in WordPress 3.4+
	 * e.g., General, Header, Footer, Content, Sidebar
	 */
	static function add_sections( $wp_customize ) {
		$i = 950;
		foreach ( self::get_settings() as $group => $elements ) {
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
	public static function add_items( $group_id, $elements ) {

		foreach ( $elements as $element ) {
			if ( $class = Styles_Helpers::get_element_class( $element ) ) {
				$class::add_item( $group_id, $element );
			}
		}

	}

}