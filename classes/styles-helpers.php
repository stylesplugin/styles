<?php

class Styles_Helpers {

	static public $prefix = 'st_';

	static public function sanitize_type( $type ) {
		$type = str_replace( array('-', '_'), ' ', $type );
		$type = ucwords( $type );
		$type = str_replace( ' ', '_', $type );
		return $type;
	}

	static public function get_group_id( $group ) {
		return self::$prefix . $group;
	}

	static public function get_setting_id( $group, $id ) {
		return "styles[$group][$id]";;
	}

	/**
	 * Return name of static class for Customizer Control based on "type"
	 */
	public static function get_element_class( $element ) {
		$type = self::sanitize_type( $element['type'] );

		// e.g., Styles_Background_Color
		$class = "Styles_$type"; 

		if ( class_exists( $class ) ) {
			return $class;
		}else {

			$file = dirname( __FILE__ ) . '/' . strtolower( str_replace('_', '-', $class ) ) . '.php';

			if ( file_exists( $file ) ) {
				include $file;
				return $class;
			}else {
				trigger_error( 'Could not find class ' . $class, E_USER_ERROR );
				return false;
			}

		}

	}

	public static function get_element_id( $element ) {
		return sanitize_key( $element['label'] . '_' . $element['type'] );
	}

	public static function get_element_setting_value( $group, $element ) {
		$settings = get_option('styles');

		$group_id = self::get_group_id( $group );
		$id = self::get_element_id( $element );

		if ( !empty( $settings[ $group_id ][ $id ] ) ) {
			return $settings[ $group_id ][ $id ];
		}else {
			return false;
		}
	}
}