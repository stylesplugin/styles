<?php

class Styles_CSS {

	static public function output_css() {
		echo '<style id="storm_styles">';

		foreach ( Styles_Customizer::get_settings() as $group => $elements ) {
			foreach ( $elements as $element ) {
				if ( $class = Styles_Helpers::get_element_class( $element ) ) {

					echo $class::get_css( $group, $element );
				
				}
			}
		}

		echo '</style>';
	}

}
