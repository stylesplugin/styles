<?php

/**
 * Detect and initialize plugins meant to extend Styles in common ways
 * For example, add customize.json for a theme
 */
class Styles_Child {

	/**
	 * Styles_Plugin
	 */
	var $plugin;

	/**
	 * Array of plugin objects
	 * @var array
	 */
	var $plugins = array();

	/**
	 * Array of inactive plugin objects
	 * @var array
	 */
	var $inactive_plugins = array();

	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		add_action( 'plugins_loaded', array( $this, 'plugins_loaded'), 20 );
		add_action( 'update_option_active_plugins', array( $this, 'update_option_active_plugins' ) );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$this->update_option_active_plugins();
		}
	}

	/**
	 *	Build $this->plugins, a list of Styles child plugins based on plugin headers
	 *
	 * @return void
	 */
	public function plugins_loaded( $plugins ) {
		if ( !function_exists( 'is_plugin_active') ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		foreach ( $this->get_plugins_meta() as $meta ) {

			$class = $meta['styles class'];
			
			if ( class_exists( $class ) ) {
				// For example,
				// new Styles_Child_Theme( $meta )
				if ( is_plugin_active( $meta['slug'] ) ) {
					$this->plugins[] = new $class( $meta );
				}else {
					$this->inactive_plugins[] = new $class( $meta );
				}
			}
		}
	}

	public function get_plugins_meta() {
		$child_plugins_meta = get_site_transient( 'styles_child_plugins' );

		if ( false !== $child_plugins_meta ) {
			return $child_plugins_meta;
		}

		if ( !function_exists( 'get_plugins') ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$child_plugins_meta = array();

		foreach ( get_plugins() as $slug => $meta ) {
			// Only include Styles child plugins with styles_class set in header
			if ( empty( $meta['styles class'] ) ) { continue; }

			$meta['slug'] = $slug;

			$child_plugins_meta[] = $meta;
		}

		// Refresh plugin list and metadata every 6 hours (or on activate/deactivate)
		set_site_transient( 'styles_child_plugins', $child_plugins_meta, 60*60*6 );
		
		return $child_plugins_meta;
	}

	/**
	 * Clear child plugins transient cache any time plugins are activated or deactivated.
	 */
	public function update_option_active_plugins() {
		delete_site_transient( 'styles_child_plugins' );
	}

}