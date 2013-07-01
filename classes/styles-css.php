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
	public $google_fonts = array();

	function __construct( $plugin ) {
		$this->plugin = $plugin;

		add_filter( 'styles_pre_get_css', array( $this, 'selector_prefix' ) );

	}

	public function selector_prefix( $element ) {
		if ( !empty( $element['selector'] ) ) {
			$selector_array = explode( ',', $element['selector'] );

			foreach( $selector_array as &$sub_selector ) {

				if ( 'body' == $sub_selector ) {
					// body selector without class; add it
					$sub_selector = $sub_selector . '.' . $this->plugin->body_class;
				}else if ( 'body ' == substr( $sub_selector, 0 ) ){
					// body selector with sub-item without class
					$sub_selector = str_replace( 'body ', 'body'. $this->plugin->body_class . ' ', $sub_selector );
				}else if ( 'html' == substr( $sub_selector, 0 ) || 'body' == substr( $sub_selector, 0 ) ) {
					// html or body selector
					continue;
				}else {
					// All others, prepend body class
					$sub_selector = '.' . $this->plugin->body_class . ' ' . $sub_selector;
				}

			}

			$element['selector'] = implode( ',', $selector_array );
		}

		return $element;
	}

	/**
	 * Rebuild CSS
	 *
	 * Cache check called in Styles_Plugin::get_css to avoid initializing this class
	 */
	public function get_css() {
		global $wp_customize;

		$css = '';

		$this->plugin->customize_register( $wp_customize );

		foreach ( $this->plugin->customize->get_settings() as $group => $elements ) {
			foreach ( $elements as $element ) {
				if ( $class = Styles_Helpers::get_element_class( $element ) ) {

					$element = apply_filters( 'styles_pre_get_css', $element );
					$control = new $class( $group, $element );

					$css .= $control->get_css();
				}
			}
		}

		$css = apply_filters( 'styles_css_output', $css );
	
		$css = implode( '', $this->google_fonts ) . $css;

		$css = $this->minify( $css );

		update_option( Styles_Helpers::get_option_key( 'css' ), $css );

		return $css;

	}

	/**
	 * Minimize CSS output using CSS Tidy.
	 * 
	 * @see styles_css_output filter
	 * @author JetPack by Automattic
	 */
	public function minify( $css ) {
		// Allow minification to be disabled with add_filter( 'styles_minify_css', '__return_false' );
		if ( !apply_filters( 'styles_minify_css', true ) ) {
			return $css;
		}

		if ( !class_exists( 'csstidy') ) {
			include dirname( __FILE__ ) . '/csstidy/class.csstidy.php';
		}

		$csstidy = new csstidy();
		$csstidy->optimize = new csstidy_optimise( $csstidy );

		$csstidy->set_cfg( 'remove_bslash',              false );
		$csstidy->set_cfg( 'compress_colors',            true );
		$csstidy->set_cfg( 'compress_font-weight',       true );
		$csstidy->set_cfg( 'remove_last_;',              true );
		$csstidy->set_cfg( 'case_properties',            true );
		$csstidy->set_cfg( 'discard_invalid_properties', true );
		$csstidy->set_cfg( 'css_level',                  'CSS3.0' );
		$csstidy->set_cfg( 'template',                   'highest');
		$csstidy->parse( $css );

		$css = $csstidy->print->plain();

		return $css;
	}

}
