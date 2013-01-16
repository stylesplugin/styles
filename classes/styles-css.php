<?php

class Styles_CSS {

	/**
	 * @var Styles_Plugin
	 */
	var $plugin;

	/**
	 * @import declarations to be added to top of CSS
	 *
	 * @var string
	 */
	public $google_fonts = '';

	function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	public function output_css() {
		global $wp_customize;

		$css = false;

		if ( empty( $wp_customize ) ) {
			$css = get_option( 'styles_cache' );
		}

		if ( !empty( $wp_customize ) || empty( $css ) ) {
			// Refresh

			$css = '';

			$this->plugin->customize_register();

			foreach ( $this->plugin->customize->get_settings() as $group => $elements ) {
				foreach ( $elements as $element ) {
					if ( $class = Styles_Helpers::get_element_class( $element ) ) {

						$css .= $class::get_css( $group, $element );
					
					}
				}
			}
		}

		$css = $this->google_fonts . $css;

		update_option( 'styles_cache', $css );
		echo '<style id="storm-styles">' . $css . '</style>';

	}

}
