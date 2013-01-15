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
	 * @var int
	 **/
	var $version = '0.5.3';
	
	/**
	 * Plugin DB version
	 * 
	 * Holds the current plugin database version. 
	 * Not the same as the current plugin version.
	 * 
	 * @var int
	 **/
	var $db_version = '0.5.0';

	var $css;

	public function __construct() {

		add_action( 'customize_register', array( $this, 'customize_register' ), 10 );
		add_action( 'wp_head', array( $this, 'wp_head' ), 999 );
		
	}

	/**
	 * Add settings to WP Customizer
	 */
	public function customize_register( $wp_customize ) {
		require_once dirname( __FILE__ ) . '/styles-helpers.php';
		require_once dirname( __FILE__ ) . '/styles-customizer.php';

		Styles_Customizer::add_sections( $wp_customize );
	}

	/**
	 * Output CSS
	 */
	public function wp_head() {
		require_once dirname( __FILE__ ) . '/styles-helpers.php';
		require_once dirname( __FILE__ ) . '/styles-css.php';
		$this->css = new Styles_CSS();
		$this->css->output_css();
	}

}