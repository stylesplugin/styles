<?php
/**
 * Uninstalls the pd-styles options when an uninstall has been requested 
 * from the WordPress admin
 *
 * @package pd-styles
 * @subpackage uninstall
 * @since 0.1
 */

// If uninstall/delete not called from WordPress then exit
if( ! defined ( 'ABSPATH' ) && ! defined ( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

// Delete shadowbox option from options table
delete_option ( 'pd-styles' );

// Remove files
#	$upload_dir = wp_upload_dir ();
#	$shadowbox_dir = "{$upload_dir['basedir']}/shadowbox-js/";
#	if ( is_dir ( $shadowbox_dir ) )
#		@rmdir ( $shadowbox_dir );