<?php
/**
 * Upgrades from versions below 1.1.0
 */

class Styles_Upgrade_1_1_0 extends Styles_Upgrade {

	/**
	 * New version number to set when this upgrade is over.
	 * This must be correct, or the updater will run in an infinite loop.
	 * 
	 * @var string
	 */
	const NEW_DB_VERSION = '1.1.0';

	/**
	 * @var array Options before the upgrade scripts.
	 */
	var $old_options;

	/**
	 * @var SFM_Plugin Instance of Styles Font Menu
	 */
	var $font_menu;

	/**
	 * Fonts found in settings, but not found in Standard or Google Fonts.
	 * Likely non-latin fonts.
	 * 
	 * @var array
	 */
	var $unrecognized_fonts = array();

	function __construct() {
		parent::__construct();

		require_once dirname( dirname( __FILE__ ) ) . '/styles-font-menu/plugin.php';
		$this->font_menu = SFM_Plugin::get_instance();

		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

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

			$this->old_options = $this->backup_before_upgrade( $option_key, '1.1.0' );

			$this->upgrade_font_families( $option_key );
		}

		// This must be updated to avoid the updater running in an infinite loop
		$plugin->set_option( 'db_version', self::NEW_DB_VERSION );
	}

	/**
	 * Upgrade text controls to use new JSON format from styles-font-menu
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

				// If no Standard Font found, check Google Fonts
				if ( empty( $new_font_family ) ) {
					$new_font_family = $this->upgrade_google_fonts( $values );
				}

				if ( false === $new_font_family && !empty( $values['font_family'] ) ) {

					$this->unrecognized_fonts[] = $values['font_family'];

				}

				$values['font_family'] = $new_font_family;

			}
		}

		update_option( $option_key, $groups );
	}

	public function upgrade_standard_fonts( $values ) {
		$standard_fonts = $this->font_menu->standard_fonts->get_fonts();
		foreach( (array) $standard_fonts as $font ) {
			if ( $values['font_family'] == $font->name ) {

				return $font->__tostring();

			}
		}

		return false;
	}

	public function upgrade_google_fonts( $values ) {
		$google_fonts = $this->font_menu->google_fonts->get_fonts();

		foreach( (array) $google_fonts as $font ) {
			if ( $values['font_family'] == $font->name ) {
				
				return $font->__tostring();

			}
		}

		return false;
	}

	public function admin_notices() {
		if ( !empty( $this->unrecognized_fonts ) ) {
			$fonts = implode( ', ', $this->unrecognized_fonts );
			?>
			<div class="updated">
				<p>
					<?php _e( 'These fonts were not able to be upgraded:', 'styles' ); ?>
					<br/>
					<code><?php echo $fonts ?></code>
				</p>
				<p>
					<?php _e( 'Sorry for the inconvenience. Only Latin fonts are supported in this version of Styles. Please use another font.', 'styles' ); ?>
				</p>
			</div>
			<?php
		}
	}

}

new Styles_Upgrade_1_1_0();