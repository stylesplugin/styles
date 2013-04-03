<?php
/**
 * Uninstalls the Styles options when an uninstall has been requested 
 * from the WordPress admin
 */

// If uninstall/delete not called from WordPress then exit
if( ! defined ( 'ABSPATH' ) && ! defined ( 'WP_UNINSTALL_PLUGIN' ) )
	exit;


global $wpdb;

$sql = "DELETE from $wpdb->options WHERE option_name LIKE 'storm-styles-%'";

if( is_multisite() ){
	
	// Site network: remove options from each blog
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
	if( $blog_ids ){

		foreach( $blog_ids as $id ) {
			switch_to_blog( $id );

			$wpdb->query( $sql );
			
			restore_current_blog();
		}

	}

}else {
	// Single site
	$wpdb->query( $sql );
}