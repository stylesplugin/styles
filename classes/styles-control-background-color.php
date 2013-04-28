<?php

class Styles_Control_Background_Color extends Styles_Control {
	var $suffix = 'Background Color';
	var $template = '$selector { background-color: $value; }';

	public function __construct( $group, $element ) {
		parent::__construct( $group, $element );
	}

	/**
	 * Register item with $wp_customize
	 */
	public function add_item() {
		global $wp_customize;

		$args = array(
			'default'    => $this->default,
			'type'       => 'option',
			'capability' => 'edit_theme_options',
			'transport'  => $this->get_transport(),
		);

		$wp_customize->add_setting( $this->setting, $args );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, Styles_Helpers::get_control_id( $this->id ), array(
			'label'    => __( $this->label, 'styles' ),
			'section'  => $this->group,
			'settings' => $this->setting,
			'priority' => $this->priority . $this->group_priority,
		) ) );

	}

	/**
	 * Return CSS based on setting value
	 */
	public function get_css(){
		$value = $this->get_element_setting_value();

		$css = '';
		if ( $value ) {
			$args = array(
				'template' => $this->template,
				'value' => $value,
			);
			$css = $this->apply_template( $args );
		}

		// Filter effects final CSS output, but not postMessage updates
		return apply_filters( 'styles_css_background_color', $css );
	}

	public function post_message( $js ) {
		$selector = str_replace( "'", "\'", $this->selector );

		$js .= str_replace(
			array( '@setting@', '@selector@' ),
			array( $this->setting, $selector ),
			file_get_contents( STYLES_DIR . '/js/post-message-part-background-color.js' )
		);

		return $js . PHP_EOL;
	}

}