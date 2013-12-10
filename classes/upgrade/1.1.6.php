<?php
/**
 * Upgrades from versions below 1.1.6
 *
 * Rebuilds CSS.
 * Standard font-families with spaces have been fixed.
 */

class Styles_Upgrade_1_1_6 extends Styles_Upgrade {

	/**
	 * New version number to set when this upgrade is over.
	 * This must be correct, or the updater will run in an infinite loop.
	 * 
	 * @var string
	 */
	const NEW_DB_VERSION = '1.1.6';

	/**
	 * @var array Options before the upgrade scripts.
	 */
	var $old_options;

	/**
	 * @var SFM_Plugin Instance of Styles Font Menu
	 */
	var $font_menu;

	function __construct() {
		parent::__construct();

		require_once dirname( dirname( __FILE__ ) ) . '/styles-font-menu/plugin.php';
		$this->font_menu = SFM_Plugin::get_instance();

		// Defined in parent class.
		// Runs $this->upgrade_site() on single site or all sites in network.
		$this->upgrade_network();

	}

	/**
	 * Find all theme options in this site and run updates
	 * @return void
	 */
	public function upgrade_site() {
		global $wpdb;

		$plugin = Styles_Plugin::get_instance();

		// Get option keys for all Styles theme settings
		$query = "SELECT option_name
			FROM $wpdb->options
			WHERE option_name LIKE 'storm-styles-%'
			AND option_name NOT LIKE 'storm-styles-%-css'
		";

		$option_keys = $wpdb->get_col( $query );

		foreach( (array) $option_keys as $option_key ) {

			$this->old_options = $this->backup_before_upgrade( $option_key, self::NEW_DB_VERSION );

			$this->upgrade_font_families( $option_key );
		}

		// Rebuild CSS
		add_filter( 'styles_force_rebuild', '__return_true' );
		$plugin->get_css();

		// This must be updated to avoid the updater running in an infinite loop
		$plugin->set_option( 'db_version', self::NEW_DB_VERSION );
	}

	/**
	 * Upgrade standard fonts to fix font-family quote output.
	 * @param  string $option_key Option key in wp_options
	 * @return void
	 */
	public function upgrade_font_families( $option_key ) {

		$groups = get_option( $option_key );

		if ( !is_array( $groups ) ) {
			return;
		}

		foreach( $groups as $group_id => &$fields ) {
			foreach( $fields as $field_id => &$values ) {
				if ( !is_array( $fields ) ) {
					continue;
				}

				if(
					'_text' !== substr( $field_id, -5 ) // Only process text fields
					|| !isset( $values['font_family'] ) // Avoid notice that should never happen
					|| empty( $values['font_family'] )  // Skip empty fields
				) {
					continue;
				}

				$new_values = $this->remove_font_family_warnings( $values );

				if ( $new_values ) {
					$values = $new_values;
				}

			}
		}

		update_option( $option_key, $groups );
	}

	public function remove_font_family_warnings( $values ) {

		// Check if JSON doesn't start with "{" and contains "Warning"
		if (
			'{' != $values['font_family'][0]
			&& false !== strpos( $values['font_family'], 'Warning' )
		) {

			// Remove PHP warning
			$json_start = strpos( $values['font_family'], '{' );
			$values['font_family'] = substr( $values['font_family'], $json_start );

			// Add classname value
			$new_font_family = json_decode( $values['font_family'] );
			$new_font_family->classname = strtolower( preg_replace( '/[^a-zA-Z0-9]/', '', $new_font_family->name ) );

			$values['font_family'] = json_encode( $new_font_family );

			return $values;

		}

		return false;
	}

}

new Styles_Upgrade_1_1_6();