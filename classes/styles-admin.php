<?php

class Styles_Admin {

	/**
	 * @var Styles_Plugin
	 */
	var $plugin;

	function __construct( $plugin ) {
		$this->plugin = $plugin;

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

}