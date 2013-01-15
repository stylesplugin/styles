<?php

class Styles_Background_Color {
	static $suffix = 'Background Color';
	static $default = '';

	static public function get_setting( $group, $id ) {
		return "styles[$group][$id]";;
	}

	/**
	 * Register item with $wp_customize
	 */
	static public function add_item( $group, $element ) {
		global $wp_customize;

		$label = $selector = $type = '';
		extract( $element, EXTR_IF_EXISTS );

		if ( empty( $selector ) ) { return false; }
		
		$id = Styles_Helpers::get_element_id( $element );
		$setting = self::get_setting( $group, $id );

		$wp_customize->add_setting( $setting, array(
			'default'    => self::$default,
			'type'       => 'option',
			'capability' => 'edit_theme_options',
			// 'transport'      => 'postMessage',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "storm_$id", array(
			'label'    => __( $label . ' ' . self::$suffix, 'styles' ),
			'section'  => $group,
			'settings' => $setting,
			'priority' => $priority . '1',
		) ) );
	}

	/**
	 * Return CSS based on setting value
	 */
	static public function get_css( $group, $element ){
		$selector = $element['selector'];
		$value = Styles_Helpers::get_element_setting_value( $group, $element );

		return "$selector { background: $value }";
	}

}