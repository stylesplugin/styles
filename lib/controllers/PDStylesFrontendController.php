<?php
/**
 * PDStyles class for frontend actions
 * 
 * This class contains all functions and actions required for PDStyles to work in the frontend of WordPress
 * 
 * @since 0.1
 * @package pd-styles
 * @subpackage Admin
 * @author pdclark
 **/
class PDStylesFrontendController extends PDStyles {
	
	/**
	 * Setup frontend functionality in WordPress
	 *
	 * @return none
	 * @since 0.1
	 */
	function __construct () {
		parent::__construct ();
		
		// ajax hooks so that we can access WordPress within Scaffold
		add_action('parse_request', array( &$this, 'parse_request') );
		add_filter('query_vars', array( &$this, 'query_vars') );
	}
	
	/**
	 * Pass ?scaffold&file=file.css requests to CSS Scaffold
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function parse_request( $wp ) {
	    // only process requests with "?scaffold"
	    if (isset( $_GET['scaffold'] ) ) {
		
			// Would be nice to pull settings from plugin options instead
			$config['load_paths'] = array(
				get_template_directory(),
				$this->plugin_dir_path(),
			);
			
			$scaffold_include = $this->plugin_dir_path() . 'scaffold/parse.php';
			if ( ! @include( $scaffold_include ) ) {
				exit( 'Could not find ' . $scaffold_include );
			}

			exit;
	    }
	}
	
	/**
	 * Whitelist the scaffold query var for parse_request()
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function query_vars($vars) {
	    $vars[] = 'scaffold';
	    return $vars;
	}

} // END class PDStylesAdminController extends PDStyles


?>