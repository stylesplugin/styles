<?php

class Styles_CSS {

	public $google_fonts = '';

	public function output_css() {
		global $wp_customize;

		$css = false;

		if ( empty( $wp_customize ) ) {
			$css = get_option( 'styles_cache' );
		}

		if ( !empty( $wp_customize ) || empty( $css ) ) {
			// Refresh

			$css = '';

			require_once dirname( __FILE__ ) . '/styles-customizer.php';

			foreach ( Styles_Customizer::get_settings() as $group => $elements ) {
				foreach ( $elements as $element ) {
					if ( $class = Styles_Helpers::get_element_class( $element ) ) {

						$css .= $class::get_css( $group, $element );
					
					}
				}
			}
		}

		$css = $this->google_fonts . $css;

		update_option( 'styles_cache', $css );
		echo '<style id="storm_styles">' . $css . '</style>';

	}

}
