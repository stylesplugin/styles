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
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_notices' ), 11 );

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
		if ( $this->is_plugin_update_or_delete() ) {
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
		if ( $this->is_plugin_update_or_delete() ) {
			return false;
		}
		if ( !is_a( $this->plugin->child, 'Styles_Child' ) ) {
			return false;
		}

		foreach ( (array) $this->plugin->child->inactive_plugins as $plugin ) {
			if ( $plugin->is_target_theme_active() ) {
				// This plugin is for the active theme, but is inactive
				$theme_name = $plugin->theme->get('Name');
				$url = wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin=' . $plugin->plugin_basename ), 'activate-plugin_' . $plugin->plugin_basename );
				$this->notices[] = "<p><strong>{$plugin->name}</strong> is installed, but not active. To add Styles support for $theme_name, please <a href='$url'>activate {$plugin->name}</a>.</p>";
			}
		}

	}

	public function is_plugin_update_or_delete() {
		if ( 'update.php' == basename( $_SERVER['PHP_SELF'] )
			|| ( isset( $_GET['action'] ) && 'delete-selected' == $_GET['action'] )
			|| !current_user_can('install_plugins')
		){
			return true;
		}else {
			return false;
		}
	}

	public function admin_notices() {
		foreach( $this->notices as $key => $message ) {
			echo "<div class='updated fade' id='styles-$key'>$message</div>";
		}
	}

	/**
	 * Pass notices to styles-customize-controls.js
	 */
	public function customize_notices() {
		wp_localize_script( 'styles-customize-controls', 'wp_styles_notices', $this->notices );
	}

	function license_menu() {
		$plugins = apply_filters( 'styles_license_form_plugins', array() );

		if ( !empty( $plugins ) ) {
			add_plugins_page( 'Styles Licenses', 'Styles Licenses', 'manage_options', 'styles-license', array( $this, 'license_page' ) );
		}
	}

    function license_page() {
        require_once STYLES_DIR . '/views/licenses.php';
        exit;
    }

}