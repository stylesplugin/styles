<?php

if ( !class_exists( 'Styles_Control_Color' ) ) {
	require_once dirname( __FILE__ ) . '/styles-control-color.php';
}

class Styles_Control_Border_Color extends Styles_Control_Color {
	var $suffix = 'border color';
	var $property = 'border-color';
	var $template = '$selector { $property: $value; }';

	public function __construct( $group, $element ) {
		parent::__construct( $group, $element );

		// Replace $property in $template for child classes
		$this->template = str_replace( '$property', $this->property, $this->template );
	}

	public function post_message( $js ) {
		$js .= str_replace(
			array( '@setting@', '@selector@', '@property@' ),
			array( $this->setting, $this->jquery_selector(), $this->property ),
			file_get_contents( STYLES_DIR . '/js/post-message-part-border-color.js' )
		);

		return $js . PHP_EOL;
	}

}