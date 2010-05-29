<?php

// Menu structure
	// Read Menu
		// From WP Menus
		// From Menubar plugin
		// From Thesis
	
	// Write Menu
		// Output via do_action
		// Output via premium theme hook detection
			// Thesis
			// Hybrid
		// Enqueue CSS
		
// Settings
	// Adjust menu styling via Admin GUI
	// Output CSS file
	// Output javascript
	
// CSS
	// units: px or em
	// Tabs
		// Borders, colors, margins, padding
	// Submenus
		// Borders, colors, margins, padding
	// Vertical or Horizontal menus
	// Background images

// Javascript
	// Animations
	// Fade-in & Out
	
	
// Impliment multiple version of one class: listing 9.03
// Impliment different classes based on sitation: listing 4.03.php

######################################################
######################################################
######################################################
######################################################
######################################################


# define Plugin constants
define( 'PDM_VERSION',				'0.0.1'		);	
define( 'PDM_DB_VERSION',			'0.0.1'		);	
define( 'PDM_PURGE_DATA',			'0'			);		//  When plugin is deactivated, if 'true', all tables, and options will be removed.

// define( 'WP_ADMIN_PATH', ABSPATH . '/wp-admin/');  // If you have a better answer to this Constant, feel free to send me an e-mail.  

define( 'PDM_FILE',       basename(__FILE__) );
define( 'PDM_FILE_PATH',  __FILE__);
define( 'PDM_NAME',       basename(__FILE__, ".php") );
define( 'PDM_PATH',       str_replace( '\\', '/', trailingslashit(dirname(__FILE__)) ) );
define( 'PDF_PATH_REL',   str_replace(ABSPATH, '/', PDM_PATH));
define( 'PDM_URL',        plugins_url('', __FILE__) );  // NOTE: It is recommended that every time you reference a url,
														// that you specify the plugins_url('xxx.xxx',__FILE__), WP_PLUGIN_URL,
														// WP_CONTENT_URL, WP_ADMIN_URL view the video by Will Norris.
define( 'PDM_LIB',        PDM_PATH.'/lib');

require_once( PDM_PATH . 'load-css-and-js.php' );
require_once( PDM_PATH . 'functions.php' );
require_once( PDM_PATH . 'menus.php' );
require_once( PDM_PATH . 'pages/options.php' );

// Output DB as XML CSS constants
require_once( PDM_PATH . 'lib/xml_constants/xml_constants.php' );

register_activation_hook(__FILE__,'pdm_activate');  // WordPress Hook that executes the installation
register_deactivation_hook( __FILE__, 'pdm_deactivate' ); // WordPress Hook that handles deactivation of the Plugin.

add_action('plugins_loaded', 'pdm_check_for_updates' );   // Checks if this plugin is an update from a previous version.


## ---------------------------
##	Testing
## --------------------------{
	if (!function_exists('pq')) {
		require_once PDM_LIB.'/phpQuery/phpQuery.php';
	}
	add_theme_support( 'nav-menus' );

	add_action('pd_test', 'pdm_out');

	function pdm_out() {
		$atts = array(
			'container_class' => 'pdn1',
			'echo' => false,
			'before' => '<div>',
			'after' => '</div>',
			'link_before' => '<span>',
			'link_after' => '</span>',
			'menu' => 'pd-menu-test',
			'menu_class' => 'menu sf-menu',
			#'container' => 'div',
			#'fallback_cb' => 'wp_page_menu',
			#'before_title' => '',
			#'after_title' => '',
		 );

		$menu = pdm_get( $atts );

		echo $menu;
	}
	
	
	/**
	 * Displays a navigation menu.
	 *
	 * Optional $args contents:
	 *
	 * id - The menu id. Defaults to blank.
	 * slug - The menu slug. Defaults to blank.
	 * menu_class - CSS class to use for the div container of the menu list. Defaults to 'menu'.
	 * format - Whether to format the ul. Defaults to 'div'.
	 * fallback_cb - If the menu doesn't exists, a callback function will fire. Defaults to 'wp_page_menu'.
	 * before - Text before the link text.
	 * after - Text after the link text.
	 * link_before - Text before the link.
	 * link_after - Text after the link.
	 * echo - Whether to echo the menu or return it. Defaults to echo.
	 *
	 * @todo show_home - If you set this argument, then it will display the link to the home page. The show_home argument really just needs to be set to the value of the text of the link.
	 *
	 * @since 3.0.0
	 *
	 * @param array $args Arguments
	 */
	function pdm_get( $args = array() ) {
		$defaults = array(
			'menu' => '',
			'container' => 'div',
			'container_class' => '',
			'menu_class' => 'menu',
			'echo' => true,
			'fallback_cb' => 'wp_page_menu',
			'before' => '',
			'after' => '',
			'link_before' => '',
			'link_after' => '',
			'depth' => 0,
			'walker' => '',
			'context' => 'frontend',
		);

		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'pd_nav_menu_args', $args );
		$args = (object) $args;

		if ($args->echo) {
			echo wp_nav_menu($args);
		}else {
			return wp_nav_menu($args);
		}
	}
	
##}end Testing

## ---------------------------
##	Testing AJAX
## --------------------------{
	function pdm_parse_request($wp) {
	    // only process requests with "my-plugin=ajax-handler"
	    if (array_key_exists('pdm', $wp->query_vars) ) {
			switch ($wp->query_vars['pdm']) {
				case 'ajax-handler':
					echo 'Ajax is so handled';
					break;
				case 'get-opts':
					$opts = pdm_get_options();
					FB::log($opts, '$opts');
					break;
				case 'get-css-constants':
					$opts = pdm_get_options();
					
					echo '@constants {'."\r";
					foreach ($opts as $key => $val) {
						if (is_array($val)) {
							foreach ($val as $sub_key => $sub_val) {
								echo "  \${$key}_$sub_key: $sub_val;\r";
							}
						}else {
							echo "  \${$key}: $val;\r";
						}
					}
					echo '}'."\r";
					break;
			}
			exit;
	    }
	}
	add_action('parse_request', 'pdm_parse_request');
	
	function pdm_query_vars($vars) {
	    $vars[] = 'pdm';
	    return $vars;
	}
	add_filter('query_vars', 'pdm_query_vars');
	
##}end Testing AJAX

