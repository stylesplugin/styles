<?php
/**
 * Uninstalls the Styles options when an uninstall has been requested 
 * from the WordPress admin
 */

// If uninstall/delete not called from WordPress then exit
if( ! defined ( 'ABSPATH' ) && ! defined ( 'WP_UNINSTALL_PLUGIN' ) )
	exit;

// Delete shadowbox option from options table
delete_option ( 'storm-styles' );
delete_option ( 'storm-styles-css' );

// Remove files
// $upload_dir = wp_upload_dir();
// $styles_dir = "{$upload_dir['basedir']}/styles/";
// if ( is_dir ( $styles_dir ) )
// 	@rmdir ( $styles_dir );