<?php
/**
 * All methods and actions relating to the use of the licence key API
 *
 * This code is based off the license feature of Backup Buddy (http://pluginbuddy.com/) and code by Brandon Dove used in the plugin update engine for Adsanity (https://gist.github
 * .com/f3e2a1cbd7a3a7c693f2)
 */

class Storm_Licenses {

	function __construct() {
		if ( current_user_can( 'manage_options' ) ) add_action( 'plugin_action_links_'.STYLES_BASENAME, array( $this, 'license_link' ) );
		add_action( 'wp_ajax_styles-licenses', array( $this, 'view_licenses' ) );
	}

	/**
	 * Add a license link to the plugin actions
	 *
	 * @param $value
	 * @return array
	 */
	function license_link( $value ) {
		$ajax_url = esc_url( add_query_arg( array( 'slug' => 'styles', 'action' => 'styles-licenses', '_ajax_nonce' => wp_create_nonce( 'styles-licenses' ),
		'TB_iframe' => true ), admin_url( 'admin-ajax.php' ) ) );

		$value[sizeof( $value )] = '<a href="'.$ajax_url.'" class="thickbox" title="'.esc_html__( 'Styles Licenses', 'styles' ).'">'.esc_html__( 'Licenses', 'styles' ).'</a>';
		return $value;

	}

	/**
	 * Call the view file to display the license modal window
	 */
	function view_licenses() {
		check_ajax_referer( 'styles-licenses', '_ajax_nonce' );
		require_once( STYLES_DIR.'/views/licenses.php' );

		die();
	}

}