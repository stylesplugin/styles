<?php
/**
 * PDStyles class for front-end actions
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
		
		
			add_action ( 'template_redirect' , array( &$this , 'frontend_js' ) );
		
	}
	
	/**
	 * Enqueue javascript required for the front-end editor
	 * 
	 * @return none
	 * @since 0.1
	 */
	function frontend_js() {
		if ( !current_user_can ( 'manage_options' ) ) {
			return;
		}
		
		wp_enqueue_script('pds-frontend', $this->plugin_url().'/lib/js/frontend-main.js',array('jquery'), $this->version, true);
		wp_localize_script ( 'pds-frontend' , 'pds_frontend' , array(
			'test'	 => __( 'Testing' , 'pd-styles' ) ,
			'ajaxurl'	 => admin_url('admin-ajax.php') ,
		) );
	}
	
} // END class PDStylesAdminController extends PDStyles


?>