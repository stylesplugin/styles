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
/*

	[1:31pm] HNSZ:
	Any more questions let me know
	[1:32pm] pdclark:
	Thanks so much!
	[1:33pm] HNSZ:
	It is a good idea to see how others do it. When you take a look at the codeigniter manual you will get a pretty good idea how to set it up, it is a relatively simple framework
	[1:40pm] pdclark:
	Yeah. I think I was getting confused because the "example" I'm looking at is doing a bit of a mix -- he's organizing his files like Rails/CakePHP, but isn't on top of an actual framework because it's a just a WordPress plugin.
	[1:41pm] pdclark:
	I watched a video of him lecturing on it, and he says, "Yeah, I just declare a bunch of globals here... that's not exactly best practice, but oh well."
	[1:41pm] pdclark:
	Then I'm left thinking, "well... so how should it be done".
	[1:42pm] pdclark:
	I should have thought to go look at his original example -- cakePHP/codeigniter
	[1:42pm] pdclark:
	It just seems daunting to delve into an entire framework I'm not using
	[1:42pm] HNSZ:
	I don't think the videos are made by the codeigniter ppl, the usermanual isn't that much reading.
	[1:42pm] pdclark:
	It does look very well organized. I'm digging into it.
	[1:44pm] HNSZ:
	You don't have to read the code, just see a few examples to get the jest of it.
	[1:47pm] HNSZ:
	I can tell you that you want to have a folder for controllers, models and templates. The teplates are php files with contain html code and everynow and then some php code (i.e. <?php echo $data['title']; ?> )
	[1:47pm] pdclark:
	I've just gotten that far.
	[1:48pm] pdclark:
	It was when I saw global vars being used to output data into this guys template that I started wondering what the best way to get data into the template / view was
	
	################
	[1:49pm] HNSZ:
	you can make a method in your parent controller ->load_model($name), ->load_view($name). 
	[1:51pm] pdclark:
	Ooooooh. So you're saying that if I use ->load_view($name), since the view file is included from inside the class method, that then all the class vars are available to the PHP file $name that I load.
	[1:53pm] HNSZ:
	Well, maybe it's ->load_model($name); $date = $this->model[$name]->fetch_data(); $this->view($name, $data);
	[1:54pm] HNSZ:
	There are various ways.
	[1:55pm] pdclark:
	I see. That is totally within my reach.
	[1:55pm] HNSZ:
	You could also just say $this->load_view("content", new ModelName()); And let the view get the data from the model through the right method.
	[1:55pm] pdclark:
	Thank you so so much. I really appreciate your offering up so much guidance.
	[1:56pm] HNSZ:
	I've been there, rewritten my app a few times and still didn's find the "perfect" method :p




*/
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

