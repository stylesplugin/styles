<?php

/**
 * Manage detection of scaffold files & IDs
 * 
 * @since 0.1
 * @package pd-styles
 * @author pdclark
 **/
class PDStyles_Extension_File {
	
	/**
	 * Reference to active file object in queue
	 * 
	 * @since 0.1.3
	 * @var PDStyles_Extension_Variable
	 **/
	var $active_file;
	
	/**
	 * Include paths. Used for searching for files.
	 * @var array
	 */
	// public $_load_paths = array();
	
	function __construct( $file ) {
		
		global $blog_id;

		if ( !file_exists( get_stylesheet_directory().$file ) ) {
			FB::error( 'File not found: '.get_stylesheet_directory().$file );
			return false;
		}

		// Check for cached output
		$cached_file = str_replace('.scss', '.css', $file);
		if ( !file_exists( get_stylesheet_directory().$cached_file ) ) {
			FB::error( 'Cached output not found: '.get_stylesheet_directory().$cached_file );
			return false;
		}

		if ( is_admin() || isset( $_GET['scaffold'] ) || 'admin-ajax.php' == basename($_SERVER['PHP_SELF']) ) {
			// Load up variables from SCSS if we're in Admin or running a cache save via AJAX
			$this->active_file = new PDStyles_Extension_Variable( array(
	 			'file' => get_stylesheet_directory().$file, // Absolute path
				'cache_file' => get_stylesheet_directory().$cached_file,
				'permalink' => $blog_id,
			) );
		}else {
			// Enqueue cached output if we're in frontend
			wp_enqueue_style('bsm-scaffold', get_stylesheet_directory_uri().$cached_file );
		}
		
	}

} // END class PDStyles_Extension_File