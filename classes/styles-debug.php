<?php

/**
 * Output helpful information for remote debug.
 */
class Styles_Debug {

	public function __construct() {
		
		add_action( 'wp_ajax_styles-debug', array( $this, 'wp_ajax_styles_debug' ) );
		add_action( 'wp_ajax_nopriv_styles-debug', array( $this, 'wp_ajax_styles_debug' ) );

		add_action( 'wp_ajax_styles-functions', array( $this, 'wp_ajax_functions' ) );
		add_action( 'wp_ajax_nopriv_styles-functions', array( $this, 'wp_ajax_functions' ) );

		add_action( 'wp_ajax_styles-errors', array( $this, 'wp_ajax_errors' ) );
		add_action( 'wp_ajax_nopriv_styles-errors', array( $this, 'wp_ajax_errors' ) );
		
	}

	public function wp_ajax_styles_debug() {
		
		echo $this->settings_table( $this->get_settings() );

		exit;
	}

	public function settings_table( $settings ) {
		?>
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
		<style>
			th { vertical-align: top; font-weight: bold; min-width: 250px;}
		</style>

		<table>
			<tr><th>key</th><th>setting</th>

		<?php
		foreach( $settings as $key => $setting ) {
			?>
			<tr>
				<th><?php echo $key ?></th>
				<td><pre><?php print_r( $setting ); ?></pre></td>
			</tr>
			<?php
		}
		echo '</table>';
		
	}

	public function get_settings() {
		$settings = array_merge(
			$this->get_stylesheet_settings(),
			$this->get_debug_settings(),
			$this->get_network_settings(),
			$this->get_plugin_settings(),
			$this->get_styles_settings()
		);

		return $settings;
	}

	public function get_stylesheet_settings() {
		return array(
			'stylesheet'          => get_stylesheet(),
			'template'            => get_template(),
		);
	}

	public function get_debug_settings() {
		return array(
			'WPLANG'              => WPLANG,
			'WP_DEBUG'            => WP_DEBUG ? 'true' : 'false',
			'CONCATENATE_SCRIPTS' => CONCATENATE_SCRIPTS ? 'true' : 'false',
			'SCRIPT_DEBUG'        => SCRIPT_DEBUG ? 'true' : 'false',
			'memory_limit'        => ini_get( 'memory_limit' ),
			'max_execution_time'  => ini_get( 'max_execution_time' ),
		);
	}

	public function get_network_settings() {
		$settings['network'] = array(
			'is_multisite'        => is_multisite() ? 'true' : 'false',
			'SUBDOMAIN_INSTALL'   => SUBDOMAIN_INSTALL ? 'true' : 'false',
		);

		return $settings;
	}

	public function get_plugin_settings() {
		return array(
			'active_plugins'      => $this->get_active_plugins(),
			'mu_plugins'          => wp_list_pluck( get_mu_plugins(), 'Name' ),
		);
	}

	public function get_styles_settings() {
		global $wpdb;

		$settings['storm-styles'] = maybe_serialize( get_option( 'storm-styles' ) );

		// Get theme options
		$query = "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'storm-styles-%';";
		$option_keys = $wpdb->get_col( $query );

		foreach ( $option_keys as $option_key ) {
			$settings[ $option_key ] = maybe_serialize( get_option( $option_key ) );
		}

		return $settings;
	}

	public function get_active_plugins() {
		$all_plugins = apply_filters( 'all_plugins', get_plugins() );
		$active_plugins = array();

		foreach ( (array) $all_plugins as $plugin_file => $plugin_data ) {
			if ( is_plugin_active( $plugin_file ) || is_plugin_active_for_network( $plugin_file ) ) {		
				$active_plugins[ $plugin_file ] = $plugin_data;
			}
		}

		$plugin_names = wp_list_pluck( $all_plugins, 'Name' );
		$plugin_versions = wp_list_pluck( $all_plugins, 'Version' );

		foreach( $plugin_versions as $key => $version) {
			$plugin_names[ $key ] .= ' ' . $version;
		}

		return array_values( $plugin_names );

	}

	public function wp_ajax_functions() {
		$functions_php = get_stylesheet_directory() . '/functions.php';
		if ( file_exists( $functions_php ) ) {
			$functions_php = file_get_contents( $functions_php );
		}else {
			$functions_php = false;
		}

		header( 'Content-Type: text/plain' );
		echo $functions_php;

		exit;
	}

	public function wp_ajax_errors() {
		$error_log = ini_get( 'error_log' );

		if ( file_exists( $error_log ) && is_readable( $error_log ) ) {
			header( 'Content-Type: text/plain' );

			$file = fopen( $error_log, 'r' );
			$count = 0;

			while( $count < 200 ) {
				echo fgets($file);
				$count++;
			}

			exit;
		}

		exit( 'Cannot read error log.' );
	}

}