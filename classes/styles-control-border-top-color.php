<?php
if ( !class_exists( 'Styles_Control_Border_Color' ) ) {
	require_once dirname( __FILE__ ) . '/styles-control-border-color.php';
}

class Styles_Control_Border_Top_Color extends Styles_Control_Border_Color {
	var $property = 'border-top-color';
	var $suffix = 'top border color';

	public function __construct( $group, $element ) {
		parent::__construct( $group, $element );
	}
}