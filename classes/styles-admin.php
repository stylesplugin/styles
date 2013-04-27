<?php

class Styles_Admin {

	/**
	 * @var Styles_Plugin
	 */
	var $plugin;

	/**
	 * Admin notices
	 */
	var $notices = array();

	var $default_themes = array(
		'twentyten',
		'twentyeleven',
		'twentytwelve',
		'twentythirteen',
	);

	/**
	 * @var Styles_License
	 */
	var $license;

	function __construct( $plugin ) {
		$this->plugin = $plugin;

		// Notices
		add_action( 'admin_init', array( $this, 'install_default_themes_notice' ), 20 );
		add_action( 'admin_init', array( $this, 'activate_notice' ), 30 );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

		// Scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Plugin Meta
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		// License Menu
		add_action( 'admin_menu', array( $this, 'license_menu' ) );
	}

	/**
	 * Enqueue admin stylesheet
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'storm-styles-admin', plugins_url('css/styles-admin.css', STYLES_BASENAME), array(), $this->plugin->version, 'all' );
	}

	public function plugin_row_meta( $meta, $basename ) {
		if ( STYLES_BASENAME == $basename ) {
			$meta[2] = str_replace( 'Visit plugin site', 'Get More Themes', $meta[2] );
			$meta[] = '<a class="button button-primary" href="' . network_admin_url( 'customize.php' ) . '">Customize Theme</a>';
		}
		return $meta;
	}

	public function install_default_themes_notice() {
		if ( !in_array( get_template(), $this->default_themes ) 
			|| 'update.php' == basename( $_SERVER['PHP_SELF'] )
			|| !current_user_can('install_plugins')
		) {
			return false;
		}

		$slug = 'styles-' . get_template();

		if ( !is_dir( WP_PLUGIN_DIR . '/' . $slug ) ) {
			// Plugin not installed
			$theme = wp_get_theme();
			$url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $slug), 'install-plugin_' . $slug );
			$this->notices[] = "<p>Styles is almost ready! To add theme options for <strong>{$theme->name}</strong>, please <a href='$url'>install Styles: {$theme->name}</a>.</p>";
			return true;
		}
	}

	/**
	 * If plugin for this theme is installed, but not activated, display notice.
	 */
	public function activate_notice() {
		$slug = 'styles-' . get_template();
		$plugin_file = $slug . '/' . 'plugin.php';

		if ( is_dir( WP_PLUGIN_DIR . '/' . $slug ) ) {
			if ( is_plugin_inactive( $plugin_file ) ) {
				$theme = wp_get_theme();
				$url = wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin=' . $plugin_file ), 'activate-plugin_' . $plugin_file );
				$this->notices[] = "<p><strong>Styles: {$theme->name}</strong> is installed, but not active. Please <a href='$url'>activate Styles: {$theme->name}</a>.</p>";
			}
		}
	}

	public function admin_notices() {
		foreach( $this->notices as $key => $message ) {
			echo "<div class='updated fade' id='styles-$key'>$message</div>";
		}
	}

	function license_menu() {
		$plugins = apply_filters( 'styles_license_form_plugins', array() );

		if ( !empty( $plugins ) ) {
			add_plugins_page( 'Styles Licenses', 'Styles Licenses', 'manage_options', 'styles-license', array( $this, 'license_page' ) );
		}
	}

    /**
     * Call the view file to display the license modal window
     */
    function license_page() {
        require_once dirname( dirname( __FILE__ ) ) . '/views/licenses.php';
        exit;
    }

}