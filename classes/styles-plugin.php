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
	var $version = '1.0.2';
	
	/**
	 * Plugin DB version
	 * 
	 * Holds the current plugin database version. 
	 * Not the same as the current plugin version.
	 * 
	 * @var string
	 **/
	var $db_version = '1.0';

	/**
	 * @var Styles_CSS
	 */
	var $css;

	/**
	 * @var Styles_Customize
	 */
	var $customize;

	/**
	 * @var Styles_Admin
	 */
	var $admin;

	/**
	 * @var Styles_Child
	 */
	var $child;

	var $query_var = 'styles-action';

	public function __construct() {

		require_once dirname( __FILE__ ) . '/styles-helpers.php';

		add_action( 'wp_head', array( $this, 'wp_head' ), 1000 );
		
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 15 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 1 );
		add_action( 'customize_register', array( $this, 'customize_register' ), 1 );

		// Generated javascript from settings for Customize postMessage transport
		add_filter( 'query_vars', array( $this, 'query_vars' ) );
		add_action( 'parse_request', array( $this, 'parse_request' ) );
		
	}

	/**
	 * Set up detection for child plugins that follow common patterns
	 */
	public function plugins_loaded() {
		if ( !is_a( $this->child, 'Styles_Child') ) {

			require_once dirname( __FILE__ ) . '/styles-child.php';
			require_once dirname( __FILE__ ) . '/styles-child-updatable.php';
			require_once dirname( __FILE__ ) . '/styles-child-theme.php';

			$this->child = new Styles_Child( $this );
		}
	}

	/**
	 * Setup WP Admin user interface
	 */
	public function admin_menu() {
		if ( !is_a( $this->admin, 'Styles_Admin') ) {

			require_once dirname( __FILE__ ) . '/styles-admin.php';

			$this->admin = new Styles_Admin( $this );
		}
	}

	/**
	 * Add settings to WP Customize
	 */
	public function customize_register( $wp_customize = null ) {
		if ( !is_a( $this->customize, 'Styles_Customize') ) {

			require_once dirname( __FILE__ ) . '/styles-control.php';
			require_once dirname( __FILE__ ) . '/styles-customize.php';

			$this->customize = new Styles_Customize( $this );
		}
	}

	/**
	 * Output CSS
	 */
	public function wp_head() {
		if ( !is_a( $this->css, 'Styles_CSS') ) {

			require_once dirname( __FILE__ ) . '/styles-control.php';
			require_once dirname( __FILE__ ) . '/styles-css.php';

			$this->css = new Styles_CSS( $this );
		}
		$this->css->output_css();
	}

	/**
	 * Whitelist query var to trigger custom requests
	 */
	public function query_vars( $vars ) {
		$vars[] = $this->query_var;
		return $vars;
	}

	/**
	 * Handle custom requests for our custom query_var
	 */
	public function parse_request( $wp ) {
		if ( !array_key_exists( $this->query_var, $wp->query_vars ) ) {
			return;
		}
		switch ( $wp->query_vars[ $this->query_var ] ) {
			case 'customize-preview-js':

				$this->customize_register();
				
				$this->customize->preview_js();

			break;
		}
	}

}