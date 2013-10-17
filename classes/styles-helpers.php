<?php

class Styles_Helpers {

	static public $prefix = 'st_';
	static public $control_id_counter = 0;
	static private $template;

	static public function sanitize_type( $type ) {
		$type = str_replace( array('-', '_'), ' ', $type );
		$type = ucwords( $type );
		$type = str_replace( ' ', '_', $type );
		return $type;
	}

	static public function get_group_id( $group ) {
		$id = self::$prefix . sanitize_key( $group );
		return apply_filters( 'styles_get_group_id', $id, $group );
	}

	static public function get_control_id( $id ) {
		self::$control_id_counter++;
		return self::$prefix . $id . '_' . self::$control_id_counter;
	}

	/**
	 * Return name of static class for Customizer Control based on "type"
	 */
	public static function get_element_class( $element ) {
		if ( empty( $element['type'] ) ) { return false; }
		$type = self::sanitize_type( $element['type'] );

		// e.g., Styles_Control_Background_Color
		$class = "Styles_Control_$type"; 

		if ( class_exists( $class ) ) {
			return $class;
		}else {

			$file = dirname( __FILE__ ) . '/' . strtolower( str_replace('_', '-', $class ) ) . '.php';

			if ( file_exists( $file ) ) {
				include $file;
				return $class;
			}else {
				// trigger_error( 'Could not find class ' . $class, E_USER_ERROR );
				return false;
			}

		}

	}

	public static function get_json_error( $json_file, $json_result ) {
		$path = str_replace( ABSPATH, '', $json_file );
		$url = site_url( $path );
		
		$syntax_error = 'Malformed JSON. Check for errors. PHP <a href="http://php.net/manual/en/function.json-decode.php" target="_blank">json_decode</a> does not support comments or trailing commas.';
		$template = '<h3>JSON error</h3>%s<p>Please check <code><a href="%s" target="_blank">%s</a></code></p>';

		// PHP 5.2
		if ( !function_exists( 'json_last_error' ) ) {
			if ( null == $json_result ) {
				return sprintf( $template, $syntax_error, $url, $path );
			}
			return false;
		}
		
		// PHP 5.3+
		switch ( json_last_error() ) {
			case JSON_ERROR_NONE:           return false; break;
			case JSON_ERROR_DEPTH:          $error = 'Maximum stack depth exceeded.'; break;
			case JSON_ERROR_STATE_MISMATCH: $error = 'Underflow or the modes mismatch.'; break;
			case JSON_ERROR_CTRL_CHAR:      $error = 'Unexpected control character.'; break;
			case JSON_ERROR_SYNTAX:         $error = $syntax_error; break;
			case JSON_ERROR_UTF8:           $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.'; break;
			default:                        $error = 'Unknown JSON error.'; break;
		}

		return sprintf( $template, $error, $url, $path );
	}

	public static function get_template() {
		if ( isset( self::$template ) ) {
			return self::$template;
		}

		global $wp_customize;

		if ( isset( $_GET['theme'] ) ) {
			self::$template = $_GET['theme'];
		}else if ( is_a( $wp_customize, 'WP_Customize_Manager' ) ) {
			self::$template = $wp_customize->theme()->template;
		}else {
			self::$template = get_template();
		}

		return self::$template;

	}

	public static function get_option_key( $suffix = false ) {
		if ( $suffix ) {
			return 'storm-styles-' . self::get_template() . '-' . $suffix;
		}else {
			return 'storm-styles-' . self::get_template();
		}
	}
}