<?php
/**
 * Uninstalls the StormStyles options when an uninstall has been requested 
 * from the WordPress admin
 *
 * @package StormStyles
 * @subpackage uninstall
 * @since 0.1
 */

// If uninstall/delete not called from WordPress then exit
if( ! defined ( 'ABSPATH' ) && ! defined ( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

// Delete shadowbox option from options table
delete_option ( 'StormStyles' );
delete_option ( 'StormStyles-preview' );

// Remove files
#	$upload_dir = wp_upload_dir ();
#	$shadowbox_dir = "{$upload_dir['basedir']}/shadowbox-js/";
#	if ( is_dir ( $shadowbox_dir ) )
#		@rmdir ( $shadowbox_dir );