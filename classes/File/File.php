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
	 * Depreciated, but used as key in variables storage array
	 * 
	 * @var string
	 **/
	var $active_id;
	
	/**
	 * Include paths. Used for searching for files.
	 * @var array
	 */
	// public $_load_paths = array();
	
	function __construct( $file ) {
		
		global $blog_id;
		$this->active_id = $blog_id;

		$cached_file = str_replace('.scss', '.css', $file);
		$no_cache = false;

		if ( !file_exists( get_stylesheet_directory().$file ) ) {
			FB::error( 'File not found: '.get_stylesheet_directory().$file );
			return false;
		}

		// Check for cached output
		if ( !file_exists( get_stylesheet_directory().$cached_file ) ) {
			FB::error( 'Cached output not found: '.get_stylesheet_directory().$cached_file );
			$no_cache = true;
		}

		if ( is_admin() || isset( $_GET['scaffold'] ) || 'admin-ajax.php' == basename($_SERVER['PHP_SELF']) ) {
			// Load up variables from SCSS if we're in Admin or running a cache save via AJAX
			$this->active_file = new PDStyles_Extension_Variable( array(
	 			'file' => get_stylesheet_directory().$file, // Absolute path
				'cache_file' => get_stylesheet_directory().$cached_file,
				'permalink' => $this->active_id,
			) );
			
		}else {
			
			if ( $no_cache || ( defined('BSM_DEVELOPMENT') && true === BSM_DEVELOPMENT ) ) {
				// If we're developing, force re-render on every CSS load
				// Pairs well with: if ( '127.0.0.1' == $_SERVER['SERVER_ADDR'] ) {define('BSM_DEVELOPMENT', true);}
				wp_enqueue_style('bsm-scaffold', '/?scaffold', array(), time() );
				
			}else {
				// Enqueue cached output if we're in frontend
				wp_enqueue_style('bsm-scaffold', get_stylesheet_directory_uri().$cached_file );
			}
		}
		
	}

} // END class PDStyles_Extension_File