<?php

if ( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	require dirname( __FILE__ ) . '/edd-sl-plugin-updater.php';
}

/**
 * Allow child plugins to update through WordPress updater
 */
class Styles_Child_Updatable {

	/**
	 * @var EDD_SL_Plugin_Updater
	 */
	private $updater;
	private $updates;
	private $license_option_key;

	var $plugin_file;
	var $plugin_basename;
	var $api_url;

	var $default_args = array(
		'api_url'     => 'http://stylesplugin.com',
	);

	public function __construct( $args ) {
		$args = wp_parse_args( $args, $this->default_args );

		$this->updates = !empty( $args['styles updates'] );
		$this->license_option_key = dirname( $args['slug'] ) . '-' . 'license';
		$this->plugin_file = WP_PLUGIN_DIR . '/' . $args['slug'];
		$this->plugin_basename = $args[ 'slug' ];
		$this->item_name = $this->maybe_guess_item_name( $args );
		$this->api_url = $args['api_url'];
		$this->name = $args['Name'];

		$this->init_updater( $args );
	}

	public function maybe_guess_item_name( $args ) {
		if ( !empty( $args['styles item'] ) ) {
			return $args['styles item'];
		}

		return trim( str_replace( 'Styles:', '', $args['Title'] ) );
	}

	/**
	 * Initialize EDD Updater and licensing if updates are enabled
	 */
	public function init_updater( $args ) {
		if ( !$this->updates ) { return; }

		// License activation hooks
		add_action( 'plugin_action_links_' . $this->plugin_basename, array( $this, 'plugin_action_links' ), 10, 4 );
		add_action( 'admin_init', array( $this, 'register_setting') );

		add_action( 'admin_init', array( $this, 'activate_license') );
		add_action( 'admin_init', array( $this, 'deactivate_license') );

		add_filter( 'styles_license_form_plugins', array( $this, 'styles_license_form_plugins' ) );

		// EDD Plugin Updater
		$edd_api_data = array(
			'author'    => $args['AuthorName'],
			'version'   => $args['Version'],
			'license'   => get_option( $this->license_option_key ),
			'item_name' => $this->item_name, // match EDD item post_title
		);

		$this->updater = new EDD_SL_Plugin_Updater( $args['api_url'], $this->plugin_file, $edd_api_data );

	}

	public function styles_license_form_plugins( $plugins ) {
		$plugins[] = array(
			'name' => $this->name,
			'license_option_key' => $this->license_option_key,
		);

		return $plugins;
	}

	/**
     * Add a license link to the plugin actions
     * Skips free plugins
     *
     * @param $value
     * @return array
     */
    function plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
    	if ( !current_user_can( 'manage_options' ) ) { return $actions; }

        $admin_url = admin_url( 'options-general.php?page=styles' );

        $actions[ sizeof( $actions ) ] = '<a href="' . $admin_url . '" title="' . esc_html__( 'Styles Licenses', 'styles' ) . '">' . esc_html__( 'Licenses', 'styles' ) . '</a>';
        
        return $actions;
    }

	/**
	* Creates settings in options table
	*/
	function register_setting() {
		register_setting( 'styles_licenses', $this->license_option_key, array( $this, 'sanitize_license' ) );
	}

	function sanitize_license( $new ) {

		$old = get_option( $this->license_option_key );

		if( $old && $old != $new ) {
			// new license has been entered, so must reactivate
			delete_option( $option_key . '_status' );
		}

		return $new;
	}

	function activate_license() {

		// listen for our activate button to be clicked
		if( isset( $_POST['edd_license_activate'] ) ) {

			// run a quick security check 
		 	if( ! check_admin_referer( 'edd_sample_nonce', 'edd_sample_nonce' ) ) 	
				return; // get out if we didn't click the Activate button

			// retrieve the license from the database
			$license = trim( get_option( $this->license_option_key ) );

			// data to send in our API request
			$api_params = array( 
				'edd_action'=> 'activate_license', 
				'license' 	=> $license, 
				'item_name' => urlencode( $this->item_name ) // the name of our product in EDD
			);
			
			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, $this->api_url ), array( 'timeout' => 15, 'sslverify' => false ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) )
				return false;

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			
			// $license_data->license will be either "active" or "inactive"

			update_option( $this->license_option_key . '_status', $license_data->license );

		}
	}

	function deactivate_license() {

		// listen for our activate button to be clicked
		if( isset( $_POST['edd_license_deactivate'] ) ) {

			// run a quick security check 
		 	if( ! check_admin_referer( 'edd_sample_nonce', 'edd_sample_nonce' ) ) 	
				return; // get out if we didn't click the Activate button

			// retrieve the license from the database
			$license = trim( get_option( $this->license_option_key ) );	

			// data to send in our API request
			$api_params = array( 
				'edd_action'=> 'deactivate_license', 
				'license' 	=> $license, 
				'item_name' => urlencode( $this->item_name ) // the name of our product in EDD
			);
			
			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, $this->api_url ), array( 'timeout' => 15, 'sslverify' => false ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			
			// $license_data->license will be either "deactivated" or "failed"
			if( $license_data->license == 'deactivated' ) {
				delete_option( $this->license_option_key . '_status' );
			}

		}
	}

}