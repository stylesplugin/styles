<?php

function pdm_admin_init() {
	
	wp_register_style( 'facebox',         PDM_URL . '/lib/facebox/facebox.css' );
	wp_register_style( 'farbtastic',      admin_url().'/css/farbtastic.css' );
	wp_register_style( 'pdm-options',     PDM_URL . '/lib/css/options.css' );
	wp_register_style( 'jqui',            'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );

	// CSScaffold
	// http://tayloraldridge.com/c/plugins/pd-menu/scaffold/?f=/c/plugins/pd-menu/lib/css/menu-template.css
	wp_register_style('pdm-menu-style', PDM_URL . '/scaffold/?f='.PDF_PATH_REL.'/lib/css/menu-template.css');

}
add_action('admin_init', 'pdm_admin_init');

function pdm_admin_print_css() {
	wp_enqueue_style( 'pdm-menu-style');
	wp_enqueue_style( 'facebox');
	wp_enqueue_style( 'farbtastic');
	wp_enqueue_style( 'pdm-options');
	wp_enqueue_style( 'jqui');
}
add_action('admin_print_styles-'.'pd-menu_page_pdm-submenu-options', 'pdm_admin_print_css');

function pdm_admin_load_js() {
	wp_register_script('jqui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js', array('jquery'), null, true);
	wp_register_script('pdm-options',  plugins_url('lib/js/options.js', __FILE__), array('jqui'), null, true);
	
	wp_enqueue_script('pdm-options');
}
add_action('admin_init', 'pdm_admin_load_js');


function pdm_load_js()
{
		
	/*  FACEBOX: A facebook lightbox style pop-up window.
	 *  http://famspam.com/facebox
	*/
	
	## -- Start: Facebox  ##
	if (  in_array( $_GET['page'], array( 'pdm-submenu-options', 'pdm-submenu-setup', 'pdm-submenu-setup' ) ) ) { # load js for options page
		wp_enqueue_script( 'facebox', plugins_url('lib/facebox/facebox.js', PDM_FILE_PATH), array( 'jquery' ) );
		wp_localize_script('jquery','SlidePress', array(
		'sspurl' => plugins_url('', __FILE__) . '/'
		));
	}
	## -- End: Facebox  ##


	/*  FABRASTIC: Farbtastic is a jQuery plug-in that can add one or more color picker widgets into a page through JavaScript
	 *  Website: http://acko.net/dev/farbtastic
	*/
	
	## -- Start: Fabrastic Color Picker --	
	if (  in_array( $_GET['page'],  array( 'pdm-submenu-options' ))) { # load js for options page

		## ------------------------------------------------------------------------------------------
		## Fabrastic is a circular color selector.  It uses two JavaScript routines that are located in the
		## 'swpframework/widgets' directory: 1) rgbcolor.js and 2) farbtastic.  It also uses HTML code which I
		## provided below under <!--
		## Website/Reference: ( http://acko.net/blog/farbtastic-color-picker-released )

		wp_enqueue_script( 'pdm_farbtastic', plugins_url('lib/js/pdm.farbtastic.js', __FILE__), array( 'jquery', 'farbtastic', 'rgbcolor' ) ); // this is very important
		wp_enqueue_script( 'rgbcolor', plugins_url('lib/js/rgbcolor.js', __FILE__)   );

		## Do not remove the 'pdm_insert_colorpicker' action or function unless you don't want to use farbastic.

		add_action('admin_footer', 'pdm_insert_colorpicker');
		function pdm_insert_colorpicker()
		{
			echo "\n";
			echo '<div id="pdm_farbtastic" style="display:none"> </div>'."\n";
			echo "\n";
		}
	}
	## -- End: Fabrastic Color Picker --

	
	/*	jQuery FORM VALIDATION: 
	 *  Website:  http://docs.jquery.com/Plugins/Validation#Validate_forms_like_you.27ve_never_been_validating_before.21
	 *  Website:  http://docs.jquery.com/Plugins/Validation
	*/
	
	## -- Start: jQueryValidation  ##
	## -- NOTE: Help is needed in setting up the jQueryValidation tool, if you know how to do it please send us an e-mail with explaining how it can be done.
	
	if (  in_array( $_GET['page'], array( 'pdm-submenu-options', 'pdm-submenu-setup') ) ) { # load js for options page
		//wp_enqueue_script( 'jquery_validate', plugins_url('lib/js/jquery-validate/jquery.validate.js', pdm_FILE_PATH), array( 'jquery' ) );
		//wp_enqueue_script( 'pdm_jquery_validation', plugins_url('lib/js/jquery-validate/pdm.validate.js', pdm_FILE_PATH), array( 'jquery' ) );
		
		
	}
	## -- End:  jQueryValidation   ##
	# 
		
}
add_action( 'init', 'pdm_load_js' ); # Loads JavaScript and CSS files
?>