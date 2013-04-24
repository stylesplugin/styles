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
		// 'twentyten' => 'TwentyTen',
		'twentyeleven' => 'TwentyEleven',
		'twentytwelve' => 'TwentyTwelve',
		'twentythirteen' => 'TwentyThirteen',
	);

	function __construct( $plugin ) {
		$this->plugin = $plugin;

		add_action( 'admin_init', array( $this, 'check_default_themes' ), 20 );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Enqueue admin stylesheet
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'storm-styles-admin', plugins_url('css/styles-admin.css', STYLES_BASENAME), array(), $this->plugin->version, 'all' );
	}

	public function plugin_row_meta( $meta, $basename ) {
		if ( STYLES_BASENAME == $basename ) {
			$meta[] = '<a class="button button-primary" href="' . network_admin_url( 'customize.php' ) . '">Customize Theme</a>';
		}
		return $meta;
	}

	public function check_default_themes() {
		if ( !array_key_exists( get_template(), $this->default_themes ) 
			|| 'update.php' == basename( $_SERVER['PHP_SELF'] )
			|| !current_user_can('install_plugins')
		) {
			return false;
		}

		$slug = 'styles-' . get_template();
		$plugin_file = $slug . '/' . $slug . '.php';
		$theme = $this->default_themes[ get_template() ];

		if ( is_dir( WP_PLUGIN_DIR . '/' . $slug ) ) {
			if ( is_plugin_inactive( $plugin_file ) ) {
				$url = wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin=' . $plugin_file ), 'activate-plugin_' . $plugin_file );
				$this->notices[] = "<p><strong>Styles: $theme</strong> is installed, but not active. Please <a href='$url'>activate Styles: $theme</a>.</p>";
			}else {
				// Plugin is installed and active
				return false;
			}
		}else {
			// Plugin not installed
			$url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $slug), 'install-plugin_' . $slug );
			$this->notices[] = "<p>Styles is almost ready! To add theme options for <strong>$theme</strong>, please <a href='$url'>install and activate Styles: $theme</a>.</p>";

			return true;
		}
	}

	public function admin_notices() {
		foreach( $this->notices as $key => $message ) {
			echo "<div class='updated fade' id='styles-$key'>$message</div>";
		}
	}

}