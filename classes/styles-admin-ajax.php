<?php

class Styles_Admin_Ajax {

	/**
	 * @var Styles_Plugin
	 */
	var $plugin;

	/**
	 * Admin notices
	 */
	var $notices = array();

	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		// Dismiss notices
		add_action( 'wp_ajax_styles-dismiss-notice', array( $this, 'dismiss_notice' ) );
		add_action( 'wp_ajax_styles-clear-notice-dismissals', array( $this, 'clear_notice_dismissals' ) );
	}

	public function dismiss_notice() {
		$options = get_option( 'storm-styles' );
		$key = sanitize_key( $_GET['key'] );

		if ( ! isset( $options['notices_dismissed'] ) ) {
			$options['notices_dismissed'] = array();
		}

		if ( ! in_array( $key, $options['notices_dismissed'] ) ) {
			$options['notices_dismissed'][] = $key;
		}

		echo (int) update_option( 'storm-styles', $options );

		exit;
	}

	public function clear_notice_dismissals() {
		$options = get_option( 'storm-styles' );

		$options['notices_dismissed'] = array();
		
		echo (int) update_option( 'storm-styles', $options );

		exit;
	}

}