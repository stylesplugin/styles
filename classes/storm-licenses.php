<?php
/**
 * All methods and actions relating to the use of the licence key API
 *
 * This code is based off the license feature of Backup Buddy (http://pluginbuddy.com/) and code by Brandon Dove used in the plugin update engine for Adsanity (https://gist.github
 * .com/f3e2a1cbd7a3a7c693f2)
 */

class Storm_Licenses {

	function __construct( $styles ) {
		if ( current_user_can( 'manage_options' ) ) add_action( 'plugin_action_links_'.STYLES_BASENAME, array( $this, 'license_link' ) );
		add_action( 'wp_ajax_styles-licenses', array( $this, 'view_licenses' ) );
		$this->styles = $styles;
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
		require_once( STYLES_DIR.'/views/licenses-temp.php' );

		die();
	}

	function validate_license( $license ) {
		$item_name = 'Styles License';

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => esc_attr( $license ),
			'item_name'  => urlencode( $item_name )
		);

		$response = wp_remote_get( add_query_arg( $api_params, STYLES_API_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) )
			return false;

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( $license_data->license == 'valid' ) {
			echo 'valid';
			exit;
			// this license is still valid
		} else {
			echo 'invalid';
			exit;
			// this license is no longer valid
		}
	}

	public function api_key_field() {
		$api_key = $this->styles->wp->get_option( 'api_key' );

		?>

    <input value="<?php esc_attr_e( $api_key ) ?>" name="styles_api_key" id="styles_api_key" type="text" class="regular-text" />
    <p>This license key is used for access to theme upgrades and support.

	<?php
	}

	// from original styles api
	public function remote_api( $user, $password ) {

		/*$this->styles->wp->api_options = get_transient( 'styles-api' );
		$css                           = get_option( 'styles-'.get_template() );

		if (
			!empty( $css ) // Have CSS for the current theme
			&& !empty( $this->styles->wp->api_options ) // API key doesn't need refreshing
			&& empty( $_POST['styles_api_key'] )
		) {
			// Already have CSS for this template
			// API key isn't being set
			return true;
		}*/

		// Check / Set API key
		/*if ( !empty( $_POST['styles_api_key'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'styles-options' ) ) {
			$api_key                                  = $_POST['styles_api_key'];
			$this->styles->wp->api_options['api_key'] = $api_key;
			set_transient( 'styles-api', $this->styles->wp->api_options, 60 * 60 * 24 * 7 );
		} else {
			$api_key = $this->styles->wp->get_option( 'api_key' );
		}*/

		// Setup verification request
		/*$request = array(
			'installed_themes' => array_keys( search_theme_directories() ),
			'active_theme'     => get_template(),
			'api_key'          => $api_key,
			'version'          => $this->styles->version,
		);*/

		$request = array(
			'installed_themes' => array_keys( search_theme_directories() ),
			'active_theme'     => get_template(),
			'username'         => esc_attr( $user ),
			'password'         => esc_attr( $password ),
			'version'          => $this->styles->version,
		);

		$response = wp_remote_get( 'http://stylesplugin.com?'.http_build_query( $request ) );

		var_dump( $response );

		/*if ( $response['response']['code'] != 200 || is_wp_error( $response ) ) {
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
		}*/
	}

	// from backup buddy
	public function perform_remote_request( $args ) {

		$defaults = array(
			'action'        => false,
			'body'          => array(),
			'headers'       => array(),
			'return_format' => 'json',
			'remote_url'    => false,
			'method'        => false,
		);
		$args     = wp_parse_args( $args, $defaults );

		extract( $args );

		$remote_url = $remote_url ? $remote_url : $this->remote_url;

		$body = wp_parse_args( $body, array(
			'product'    => $this->product,
			'key'        => $this->plugins[$this->plugin_slug]->key,
			'guid'       => $this->plugins[$this->plugin_slug]->guid,
			'userhash'   => $this->plugins['userhash'],
			'username'   => $this->plugins['username'],
			'action'     => $action,
			'wp-version' => get_bloginfo( 'version' ),
			'referer'    => str_replace( 'https://', 'http://', site_url() ),
			'site'       => str_replace( 'https://', 'http://', site_url() ),
			'version'    => $this->version,
		) );

		$body   = apply_filters( "pluginbuddy_remote_body_{$this->plugin_slug}", $body );
		$method = $method ? $method : $this->method;
		if ( $method == 'GET' ) {
			$remote_url = add_query_arg( $body, $remote_url );
		} else {
			$body = http_build_query( $body );
		}

		$headers = wp_parse_args( $headers, array(
			'Content-Type'   => 'application/x-www-form-urlencoded',
			'Content-Length' => is_array( $body ) ? 0 : strlen( $body )
		) );
		$headers = apply_filters( "pluginbuddy_remote_headers_{$this->plugin_slug}", $headers );

		$post = apply_filters( "pluginbuddy_remote_args_{$this->plugin_slug}", array( 'headers' => $headers, 'body' => $body, 'timeout' => 20 ) );

		//die( '<pre>' . print_r( $post, true ) );
		//Retrieve response
		if ( $method == 'GET' ) {
			$response = wp_remote_get( esc_url_raw( $remote_url ), $post );
		} else {
			$response = wp_remote_post( esc_url_raw( $remote_url ), $post );
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		//$current_plugin = $this->plugins[ 'pluginbuddy_loopbuddy' ];

		if ( $response_code != 200 || is_wp_error( $response_body ) ) {
			return false;
		}
		switch ( $return_format ) {
			case 'json':
				return json_decode( $response_body );
				break;
			case 'serialized':
				return maybe_unserialize( $response_body );
				break;
			default:
				return $response_body;
				break;
		} //end switch
		return false;
	} //end perform_remote_request

	// from backup buddy
	private function save_plugin_options( $clearhash = false ) {
		//echo 'saving';

		//Get plugin options
		$options                     = $this->get_plugin_options(); //Since multiple plugins are using the same class variable, make sure the class variable is up to date before updating it
		$options[$this->plugin_slug] = $this->plugins[$this->plugin_slug];
		if ( !empty( $this->plugins['userhash'] ) ) $options['userhash'] = $this->plugins['userhash'];
		if ( !empty( $this->plugins['username'] ) ) $options['username'] = $this->plugins['username'];
		if ( $clearhash == true ) {
			$this->plugins['userhash'] = $options['userhash'] = '';
			$this->plugins['username'] = $options['username'] = '';
		}
		if ( $this->plugin_slug == 'pluginbuddy_loopbuddy' ) {
			//die( '<pre>' . print_r( $options[ $this->plugin_slug ], true ) );
		}

		//echo '<pre>' . print_r( $options, true ) . '</pre>';

		if ( is_multisite() ) {
			$this->update_site_option( 'pluginbuddy_plugins', $options );
		} else {
			$this->update_option( 'pluginbuddy_plugins', $options );
		}
	} //end save_plugin_options

	// from backup buddy
	private function get_defaults() {
		//Fill out defaults for the global variable
		if ( !isset( $this->plugins['userhash'] ) ) {
			$this->plugins['userhash'] = '';
			$this->plugins['username'] = '';
		}

		//Fill out defaults for the individual plugin
		$plugin_options              = new stdClass;
		$plugin_options->url         = $this->plugin_url;
		$plugin_options->slug        = $this->plugin_slug;
		$plugin_options->package     = '';
		$plugin_options->new_version = $this->version;
		$plugin_options->last_update = time();
		$plugin_options->id          = "0";
		$plugin_options->key         = false;
		$plugin_options->key_status  = 'not_set';
		$plugin_options->guid        = uniqid( '' );
		return $plugin_options;
	} //end get_defaults
}