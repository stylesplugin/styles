<?php

/**
 * Plugin wrapper
 **/
class Styles_Plugin {
	
	/**
	 * Plugin Version
	 *
	 * Holds the current plugin version.
	 *
	 * @var string
	 **/
	var $version = '1.0b2';
	
	/**
	 * Plugin DB version
	 * 
	 * Holds the current plugin database version. 
	 * Not the same as the current plugin version.
	 * 
	 * @var string
	 **/
	var $db_version = '1.0b1';

	/**
	 * @var Styles_CSS
	 */
	var $css;

	/**
	 * @var Styles_Theme
	 */
	var $theme;

	/**
	 * @var Styles_Customize
	 */
	var $customize;

	/**
	 * @var Styles_Admin
	 */
	var $admin;

	public function __construct() {

		require_once dirname( __FILE__ ) . '/styles-helpers.php';

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wp_head', array( $this, 'wp_head' ), 999 );
		
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'customize_register', array( $this, 'customize_register' ), 1 );
		
	}

	public function init() {
		if ( !is_a( $this->theme, 'Styles_Theme') ) {
			require_once dirname( __FILE__ ) . '/styles-theme.php';
			$this->theme = new Styles_Theme( $this );
		}
	}

	public function admin_init() {
		if ( !is_a( $this->admin, 'Styles_Admin') ) {
			require_once dirname( __FILE__ ) . '/styles-admin.php';
			$this->admin = new Styles_Admin( $this );
		}
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