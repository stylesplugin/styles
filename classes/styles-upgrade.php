<?php

class Styles_Upgrade {

	/**
	 * @var Styles_Plugin Instance of the plugin controller.
	 */
	var $plugin;

	/**
	 * Plugin DB version
	 * 
	 * Holds the current plugin database version. 
	 * Not the same as the current plugin version.
	 * 
	 * @var string
	 **/
	var $db_version = '1.1.6';

	public function __construct() {
		$this->plugin = Styles_Plugin::get_instance();
	}

	public function maybe_upgrade() {
		if ( version_compare ( $this->plugin->get_option('db_version'), $this->db_version, '!=' ) ) {
			$this->check_upgrade();
		}
	}

	/**
	 * Check if an upgrade is needed
	 * 
	 * @return none
	 */
	public function check_upgrade() {

		if ( $this->version_compare( array( '1.1.0' => '<' ) ) ) {

			// Upgrades for versions below 1.1.0
			require_once dirname(__FILE__) . '/upgrade/1.1.0.php'; 
			
			// Check for additional upgrade
			$this->check_upgrade();
			
		}else if ( $this->version_compare ( array( '1.1.0' => '>', '1.1.3' => '<' ) ) ) {

			// Upgrades for versions below 1.1.3
			require_once dirname(__FILE__) . '/upgrade/1.1.3.php'; 
			
			// Check for additional upgrade
			$this->check_upgrade();

		}else if ( $this->version_compare ( array( '1.1.3' => '>', '1.1.6' => '<' ) ) ) {

			// Upgrades for versions below 1.1.6
			require_once dirname(__FILE__) . '/upgrade/1.1.6.php'; 
			
			// Check for additional upgrade
			$this->check_upgrade();

		}
			
	}
	
	/**
	 * Compare Versions
	 *
	 * @author Matt Martz <matt@sivel.net>
	 * @param array Array of the version you want to compare to the version stored in the database as the key and the operator as the value
	 * @return bool
	 */
	function version_compare ( $versions ) {
		foreach ( $versions as $version => $operator ) {
			if ( version_compare ( $this->plugin->get_option('db_version'), $version, $operator ) )
				$response = true;
			else
				$response = false; 
		}
		return $response;
	}

	/**
	 * Used by child upgrade scripts to iterate over all sites in network.
	 * @return void
	 */
	public function upgrade_network() {
		global $wpdb;
		
		if( is_multisite() ){
			
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

			foreach( (array) $blog_ids as $id ) {
				switch_to_blog( $id );
				$this->upgrade_site();
				restore_current_blog();
			}
		}else {

			$this->upgrade_site();

		}
	}

	public function backup_before_upgrade( $option_key, $before_version ) {
		
		$old_options = get_option( $option_key );

		set_transient(
			"$option_key-pre-$before_version",
			$old_options,
			30 * (60*60*24) // 30 * (1 day)
		);

		return $old_options;

	}

}