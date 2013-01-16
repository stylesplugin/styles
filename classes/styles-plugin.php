<?php

/**
 * Common actions between admin and front-end.
 **/
class Styles_Plugin {
	
	/**
	 * Plugin Version
	 *
	 * Holds the current plugin version.
	 *
	 * @var string
	 **/
	var $version = '0.5.3';
	
	/**
	 * Plugin DB version
	 * 
	 * Holds the current plugin database version. 
	 * Not the same as the current plugin version.
	 * 
	 * @var string
	 **/
	var $db_version = '0.5.0';

	/**
	 * @var Styles_CSS
	 */
	var $css;

	/**
	 * @var Styles_Customize
	 */
	var $customize;

	public function __construct() {

		require_once dirname( __FILE__ ) . '/styles-helpers.php';

		add_action( 'customize_register', array( $this, 'customize_register' ), 1 );
		add_action( 'wp_head', array( $this, 'wp_head' ), 999 );
		
	}

	/**
	 * Add settings to WP Customize
	 */
	public function customize_register( $wp_customize ) {
		if ( !is_a( $this->customize, 'Styles_Customize') ) {
			require_once dirname( __FILE__ ) . '/styles-customize.php';
			$this->customize = new Styles_Customize( $this );
		}
	}

	/**
	 * Output CSS
	 */
	public function wp_head() {
		if ( !is_a( $this->css, 'Styles_CSS') ) {
			require_once dirname( __FILE__ ) . '/styles-css.php';
			$this->css = new Styles_CSS( $this );
		}
		$this->css->output_css();
	}

}