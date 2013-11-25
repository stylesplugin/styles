<?php
/**
 * Upgrades from versions below 1.1.3
 *
 * Rebuilds CSS.
 * Standard font-families with spaces have been fixed.
 */

class Styles_Upgrade_1_1_3 extends Styles_Upgrade {

	/**
	 * New version number to set when this upgrade is over.
	 * This must be correct, or the updater will run in an infinite loop.
	 * 
	 * @var string
	 */
	const NEW_DB_VERSION = '1.1.3';

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

				$new_font_family = $this->upgrade_standard_fonts( $values );

				if ( $new_font_family ) {
					$values['font_family'] = $new_font_family;
				}

			}
		}

		update_option( $option_key, $groups );
	}

	public function upgrade_standard_fonts( $values ) {
		$standard_fonts = $this->font_menu->standard_fonts->get_fonts();
		
		$fonts_to_upgrade = array(
			'Century Gothic',
			'Comic Sans MS',
			'Lucida Grande',
			'Trebuchet MS',
		);

		$upgrade_font = false;

		foreach( $fonts_to_upgrade as $font ) {
			// These fonts have incorrect double-quotes
			if ( false !== strpos( $values['font_family'], '""' . $font . '"' ) ) {
				$upgrade_font = $font;
			}
		}

		if ( !empty( $upgrade_font ) ) {
			foreach( (array) $standard_fonts as $font ) {
				if ( $upgrade_font == $font->name ) {

					return $font->__tostring();

				}
			}
		}

		return false;
	}

}

new Styles_Upgrade_1_1_3();