<?php

class Styles_CSS {

	/**
	 * @var Styles_Plugin
	 */
	var $plugin;

	/**
	 * Class added to body and all selectors
	 *
	 * @var string
	 */
	var $body_class = 'styles';

	/**
	 * @import declarations to be added to top of CSS
	 *
	 * @var string
	 */
	public $google_fonts = '';

	function __construct( $plugin ) {
		$this->plugin = $plugin;

		add_filter( 'styles_pre_get_css', array( $this, 'selector_prefix' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	public function selector_prefix( $element ) {
		if ( !empty( $element['selector'] ) ) {
			$element['selector'] = '.' . $this->body_class . ' ' . $element['selector'];
		}

		return $element;
	}

	public function body_class( $classes ) {
		$classes[] = $this->body_class;
		return $classes;
	}

	public function output_css() {
		global $wp_customize;

		$css = false;

		if ( empty( $wp_customize ) ) {
			$css = get_option( 'storm-styles-css' );
		}

		if ( !empty( $wp_customize ) || empty( $css ) ) {
			// Refresh

			$css = '';

			$this->plugin->customize_register( $wp_customize );

			foreach ( $this->plugin->customize->get_settings() as $group => $elements ) {
				foreach ( $elements as $element ) {
					if ( $class = Styles_Helpers::get_element_class( $element ) ) {

						$element = apply_filters( 'styles_pre_get_css', $element );
						$css .= $class::get_css( $group, $element );
					
					}
				}
			}
		}

		$css = $this->google_fonts . $css;

		update_option( 'storm-styles-css', $css );
		echo '<style id="storm-styles">' . $css . '</style>';

	}

}
