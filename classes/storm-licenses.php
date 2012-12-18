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

	function validate_license() {
		// get license option

		// check with store site to see if license valid


		// return if license is valid or not
	}

	function get_license_from_login() {
		// send user/pass to store site, get either error message or license key in return

		// save license key to styles-api-key option
		if ( isset( $_POST['username'] ) && isset( $_POST['password'] ) ) :

			// check against remote server

			require_once( ABSPATH.WPINC.'/class-IXR.php' );

			$this->client = new IXR_Client( trailingslashit( STYLES_API_URL ).'xmlrpc.php' );

			$client_request_args = array(
				'username' => $_POST['username'],
				'password' => $_POST['password'],
				'plugin'   => STYLES_SLUG,
				'url'      => site_url()
			);

			if ( !$this->client->query( 'puengine.is_user_authorized', $client_request_args ) ) :

				add_action( 'admin_notices', array( $this, 'error_notice' ) );

				return false;

			endif;

			$api_options = get_option( 'styles-api-key', array() );

			$api_options['api-key'] = $this->client->getResponse();

			//$api_options['update-permalinks'] = 1;

			update_option( 'styles-api-key', $api_options );

			//header( 'Location: '.admin_url( 'edit.php?post_type=ads' ) );

			die();

		endif;

		//return error message or success message
	}

	function remote_api_call( $args ) {

		$request = wp_remote_post( STYLES_API_URL, array( 'body' => $args ) );

		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 )

			return false;

		$response = maybe_unserialize( wp_remote_retrieve_body( $request ) );

		if ( is_object( $response ) ) :

			return $response; else :

			return false;

		endif;
	}

	public function api_key_field() {
		$api_key = $this->styles->wp->get_option( 'api_key' );

		?>

    <input value="<?php esc_attr_e( $api_key ) ?>" name="styles_api_key" id="styles_api_key" type="text" class="regular-text" />
    <p>This license key is used for access to theme upgrades and support.

	<?php
	}

	public function remote_api() {

		$this->styles->wp->api_options = get_transient( 'styles-api' );
		$css                           = get_option( 'styles-'.get_template() );

		if (
			!empty( $css ) // Have CSS for the current theme
			&& !empty( $this->styles->wp->api_options ) // API key doesn't need refreshing
			&& empty( $_POST['styles_api_key'] )
		) {
			// Already have CSS for this template
			// API key isn't being set
			return true;
		}

		// Check / Set API key
		if ( !empty( $_POST['styles_api_key'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'styles-options' ) ) {
			$api_key                                  = $_POST['styles_api_key'];
			$this->styles->wp->api_options['api_key'] = $api_key;
			set_transient( 'styles-api', $this->styles->wp->api_options, 60 * 60 * 24 * 7 );
		} else {
			$api_key = $this->styles->wp->get_option( 'api_key' );
		}

		// Setup verification request
		$request = array(
			'installed_themes' => array_keys( search_theme_directories() ),
			'active_theme'     => get_template(),
			'api_key'          => $api_key,
			'version'          => $this->styles->version,
		);

		$response = wp_remote_get( 'http://stylesplugin.com?'.http_build_query( $request ) );

		if ( $response['response']['code'] != 200 || is_wp_error( $response ) ) {
			add_settings_error( 'styles-api-key', '404', 'Could not connect to API host. Please try again later.', 'error' );
		}

		$data = json_decode( $response['body'] );

		$this->styles->wp->api_options['api_valid']  = $data->api_valid;
		$this->styles->wp->api_options['license']    = $data->license;
		$this->styles->wp->api_options['meta_boxes'] = $data->meta_boxes;

		set_transient( 'styles-api', $this->styles->wp->api_options, $data->transient_expire );

		if ( !empty( $data->message ) ) {
			add_settings_error( 'styles-api-key', 'api-message', $data->message, $data->type );
		}
		if ( !empty( $data->supported_themes ) ) {
			$this->styles->wp->api_options['supported_themes'] = $data->supported_themes;
		}
		if ( !empty( $data->css ) ) {
			delete_option( 'styles-'.get_template() );
			add_option( 'styles-'.get_template(), $data->css, null, 'no' ); // Don't autoload
		}
	}

	function error_notice() {

		echo '<div class="error"><p>'.esc_html( $this->client->message->faultString ).'</p></div>';
	}
}