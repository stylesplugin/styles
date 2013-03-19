<?php
/**
 * All methods and actions relating to the use of the licence key API
 *
 * This code is based off the license feature of Backup Buddy (http://pluginbuddy.com/) and code by Brandon Dove used in the plugin update engine for Adsanity (https://gist.github
 * .com/f3e2a1cbd7a3a7c693f2)
 */

class Styles_Licenses {

	function __construct( $styles ) {
		if ( current_user_can( 'manage_options' ) ) add_action( 'plugin_action_links_'.STYLES_BASENAME, array( $this, 'license_link' ) );
		add_action( 'wp_ajax_styles-licenses', array( $this, 'view_licenses' ) );
		$this->styles = $styles;
	}

	/**
	 * Add a license link to the plugin actions
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

	/**
	 * Validate a manually entered license
	 * @param $license
	 * @return bool
	 */
	function validate_manual_license( $license ) {

		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => esc_attr( $license ),
			'item_name'  => urlencode( get_template() ),
			'version' => $this->styles->version, //@todo do something with version after passed?
		);

		$response = wp_remote_get( add_query_arg( $api_params, STYLES_API_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) )
			return false;

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( $license_data->license == 'valid' ) {
			$this->styles->wp->api_options['api_valid'] = $license_data->license;
			$this->styles->wp->api_options['license']   = $license_data->item_name;
			$this->styles->wp->api_options['api_key']   = $license_data->api_key;
			update_option( 'styles-api', $this->styles->wp->api_options );
			$this->styles->wp->api_options['supported_themes'] = $license_data->item_name;
			if ( !empty( $license_data->css ) ) {
				delete_option( 'styles-'.get_template() );
				add_option( 'styles-'.get_template(), $license_data->css, null, 'no' ); // Don't autoload
			}
			esc_html_e( 'License was successfully validated', 'styles' );
			exit;
			// this license is still valid
		} else {
			esc_html__( 'This is not a valid license key', 'styles' );
			exit;
			// this license is no longer valid
		}
	}

	/**
	 * Add the field to the view to manually enter a license key
	 */
	public function api_key_field() {
		$this->styles->wp->api_options = get_option( 'styles-api' );
		$api_key = $this->styles->wp->api_options['api_key'];
		?>

        <input value="<?php esc_attr_e( $api_key ) ?>" name="license" id="license" type="text" class="regular-text" />
        <p><?php esc_html_e( 'This license key is used for access to theme upgrades and support', 'styles' ); ?>.</p>
	<?php
	}

	/**
	 * Use the username and password to retrieve a license key for that user
	 * @param $user
	 * @param $password
	 * @return bool
	 */
	public function validate_user_pass( $user, $password ) {

		$request = array(
			'installed_themes' => array_keys( search_theme_directories() ),
			'active_theme'     => get_template(),
			'username'         => esc_attr( $user ),
			'password'         => esc_attr( $password ),
			'version'          => $this->styles->version, //@todo do something with version after passed?
		);

		$response = wp_remote_get( STYLES_API_URL . '?' . http_build_query( $request ) );

		if ( is_wp_error( $response ) )
			return false;

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( $license_data->license == 'valid' ) {
			$this->styles->wp->api_options['api_valid'] = $license_data->license;
			$this->styles->wp->api_options['license']   = $license_data->item_name;
			$this->styles->wp->api_options['api_key']   = $license_data->api_key;
			update_option( 'styles-api', $this->styles->wp->api_options );
			$this->styles->wp->api_options['supported_themes'] = $license_data->item_name;
			if ( !empty( $license_data->css ) ) {
				delete_option( 'styles-'.get_template() );
				add_option( 'styles-'.get_template(), $license_data->css, null, 'no' ); // Don't autoload
			}
			esc_html_e( 'License has been successfully retrieved', 'styles' );
			exit;
			// this license is still valid
		} else {
			esc_html_e( 'This user does not have any valid licenses', 'styles' );
			exit;
			// this license is no longer valid
		}

	}

}