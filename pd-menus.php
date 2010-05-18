<?php
/*
Plugin Name: PD Menus
Plugin URI: http://pdclark.com/
Description: Super-duper menus
Author: Paul Clark
Version: 0.0.1
Author URI: http://pdclark.com

Changelog:
0.0.1	Clean Slate
*/

/*  Copyright 2009 Paul Clark (email: pdclark at pdclark.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

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
	
if (!function_exists('pq')) {
	require_once dirname(__FILE__).'/phpQuery/phpQuery.php';
}
add_theme_support( 'nav-menus' );

add_action('pd_test', 'pd_menu_out');

function pd_menu_out() {
	$atts = array(
		'container_class' => 'pdn1',
		'echo' => false,
		'before' => '<div>',
		'after' => '</div>',
		'menu' => '',
		'menu_class' => 'menu sf-menu',
		#'container' => 'div',
		#'fallback_cb' => 'wp_page_menu',
		#'before_title' => '',
		#'after_title' => '',
	 );

	$menu = pd_nav_menu2( $atts );
	
	echo $menu;
	exit;
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
function pd_nav_menu2( $args = array() ) {
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

/*
 * register with hook 'wp_print_styles'
 */
add_action('wp_print_styles', 'add_my_stylesheet');

/*
 * Enqueue style-file, if it exists.
 */

function add_my_stylesheet() {
   $myStyleUrl = WP_PLUGIN_URL . '/pd-menus/style.css.php';
   $myStyleFile = WP_PLUGIN_DIR . '/pd-menus/style.css.php';
   if ( file_exists($myStyleFile) ) {
       wp_register_style('myStyleSheets', $myStyleUrl);
       wp_enqueue_style( 'myStyleSheets');
   }
}


add_action('admin_init', 'pd_nav_menu_setup');

/**
 * Register nav menu metaboxes and advanced menu items
 *
 * @since 3.0.0
 **/
function pd_nav_menu_setup() {
	// Register meta boxes
	add_meta_box( 'add-pd-nav-options', __('PD Menus'), 'pd_nav_menu_item_link_meta_box', 'nav-menus', 'side', 'low' );
	#	wp_nav_menu_post_type_meta_boxes();
	#	wp_nav_menu_taxonomy_meta_boxes();
    #	
	#	// Register advanced menu items (columns)
	#	add_filter( 'manage_nav-menus_columns', 'wp_nav_menu_manage_columns');
    #	
	#	add_filter( 'columns_prefs_header', create_function( '', "return __('Show advanced menu properties');" ));
    #	
	#	// If first time editing, disable advanced items by default.
	#	if( false === get_user_option( 'managenav-menuscolumnshidden' ) ) {
	#		$user = wp_get_current_user();
	#		update_user_option($user->ID, 'managenav-menuscolumnshidden',
	#			array( 0 => 'link-target', 1 => 'css-classes', 2 => 'xfn', 3 => 'description', ),
	#			true);
	#	}
	
	
	wp_enqueue_script( 'jquery');
	//wp_enqueue_script( 'jquery-form');
	//wp_enqueue_script( 'jquery-color');
	//wp_enqueue_script( 'jquery-ui-core');
	//wp_enqueue_script( 'jquery-ui-tabs');
	//wp_enqueue_script( 'jquery-ui-sortable');
	//wp_enqueue_script( 'jquery-ui-draggable');
	//wp_enqueue_script( 'jquery-ui-droppable');
	//wp_enqueue_script( 'jquery-ui-selectable');
	//wp_enqueue_script( 'jquery-ui-resizable');
	//wp_enqueue_script( 'jquery-ui-dialog');
	//wp_enqueue_script( 'interface');
	//wp_enqueue_script( 'schedule');
	//wp_enqueue_script( 'suggest');
}

/**
 * Displays a metabox for the custom links menu item.
 *
 * @since 3.0.0
 */
function pd_nav_menu_item_link_meta_box() {
	//	global $_nav_menu_placeholder;
	//	$_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;
    //	
	//	// @note: hacky query, see #12660
	//	$args = array( 'post_type' => 'nav_menu_item', 'post_status' => 'any', 'meta_key' => '_menu_item_type', 'numberposts' => -1, 'orderby' => 'title', );
    //	
	//	// @todo transient caching of these results with proper invalidation on updating links
	//	$links = get_posts( $args );
    //	
	//	$current_tab = 'create';
	//	if ( isset( $_REQUEST['customlink-tab'] ) && in_array( $_REQUEST['customlink-tab'], array('create', 'all') ) ) {
	//		$current_tab = $_REQUEST['customlink-tab'];
	//	}
    //	
	//	$removed_args = array(
	//		'action',
	//		'customlink-tab',
	//		'edit-menu-item',
	//		'menu-item',
	//		'page-tab',
	//		'_wpnonce',
	//	);
	

	?>
	<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script> -->
    <style type="text/css">
    #slider { margin: 10px; }
  </style>

	
	<div class="pdnavdiv">
			
			<div id="slider"></div>
			
			<input type="hidden" value="custom" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" />
			<p id="menu-item-url-wrap">
				<label class="howto" for="custom-menu-item-url">
					<span><?php _e('URL'); ?></span>
					<input id="custom-menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" type="text" class="code menu-item-textbox" value="http://" />
				</label>
			</p>

			<p id="menu-item-name-wrap">
				<label class="howto" for="custom-menu-item-name">
					<span><?php _e('Label'); ?></span>
					<input id="custom-menu-item-name" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" type="text" class="regular-text menu-item-textbox input-with-default-title" title="<?php esc_attr_e('Menu Item'); ?>" />
				</label>
			</p>

		<p class="button-controls">
			<span class="list-controls">
				<a href="#" class="select-all add-home-link"><?php _e('Add Home Link'); ?></a>
			</span>
			<span class="add-to-menu">
				<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
				<input type="submit" class="button-secondary" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-custom-menu-item" />
			</span>
		</p>
		
		

		<div class="clear"></div>
	</div><!-- /.customlinkdiv -->
	<?php
}

/*
    * This example will work at least on WordPress 2.6.3, 
    * but maybe on older versions too.
    */

   add_action('admin_init', 'my_plugin_admin_init');
   add_action('admin_menu', 'my_plugin_admin_menu');
   
   function my_plugin_admin_init()
   {
       /* Register our script. */
		wp_register_script('pd-menus-admin', WP_PLUGIN_URL . '/pd-menus/admin.js', array('jqui'), null, true);
		wp_register_script('jqui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js', array('jquery'), null, true);
		
		wp_enqueue_script('pd-menus-admin');
		
		
		
   }
   
   function my_plugin_admin_menu()
   {
       /* Register our plugin page */
       $page = add_submenu_page( 'themes.php', 
                                 __('PD Menu Styles', 'pd_menu'), 
                                 __('PD Menu Styles', 'pd_menu'), 9,  __FILE__, 
                                 'pd_menu_styles_manage_menu');
  
       /* Using registered $page handle to hook script load */
       add_action('admin_print_scripts-' . $page, 'pd_menu_admin_styles');
   }
   
   function pd_menu_admin_styles()
   {
       /*
        * It will be called only on your plugin admin page, enqueue our script here
        */
       wp_enqueue_script('pd-menus-admin');
   }
   
   function pd_menu_styles_manage_menu()
   {
		echo pd_nav_menu2();
		?>
			Yo homeh.
		<?php
		pd_nav_menu_item_link_meta_box();
   }





##	#  ==========================
##	#	Functions
##	#  ========================={
##		
##		function pd_nav_menu() {
##			global $pd_nav;
##	
##			if (is_a($pd_nav, 'PdNav')) {
##				$menu = &$pd_nav;
##			}else {
##				$menu = new PdNav;
##			}
##	
##			$menu->output();
##		}
##	
##		function pd_nav_menu_centered() {
##			$menu = new PdNav;
##			$menu->centered = true;
##			$menu->output();
##		}
##	
##		function multi_array_key_exists($needle, $haystack) {
##		      foreach ($haystack as $key=>$value) {
##		        if ($needle===$key) {
##		          return $key;
##		        }
##		        if (is_array($value)) {
##		          if(multi_array_key_exists($needle, $value)) {
##		            return $key . ":" . multi_array_key_exists($needle, $value);
##		          }
##		        }
##		      }
##		  return false;
##		}
##		
##	#}end Functions
##	
##	#  ==========================
##	#	Classes
##	#  ========================={
##		class ThesisMenubarIntegration{
##	
##			function activation_hook(){
##				## Modify OpenHook
##				global $wpdb;
##				
##				$sql = "UPDATE
##							$wpdb->options
##						SET
##							option_value = replace(option_value,'thesis_nav_menu();','pd_nav_menu();')
##						WHERE
##							option_name LIKE 'openhook_%'
##						";
##						
##				$wpdb->query($sql);
##				
##				## Regular activation
##				$thesis_menubar_integration = plugin_basename(__FILE__);
##			
##				$active_plugins = get_option('active_plugins');
##			
##				$new_active_plugins = array();
##			
##				array_push($new_active_plugins, $thesis_menubar_integration);
##			
##				foreach($active_plugins as $plugin)
##						if($plugin!=$thesis_menubar_integration) $new_active_plugins[] = $plugin;
##			
##			
##				update_option('active_plugins',$new_active_plugins);
##			}
##			
##			function deactivation_hook(){
##				global $wpdb;
##				
##				$sql = "UPDATE
##							$wpdb->options
##						SET
##							option_value = replace(option_value,'pd_nav_menu();','thesis_nav_menu();')
##						WHERE
##							option_name LIKE 'openhook_%'
##						";
##						
##				$wpdb->query($sql);
##			}
##		
##			function pre_update_option_active_plugins($newvalue){
##			
##				$thesis_menubar_integration = plugin_basename(__FILE__);
##			
##				if(!in_array($thesis_menubar_integration,$newvalue)) return $newvalue;
##			
##				$new_active_plugins = array();
##			
##				array_push($new_active_plugins, $thesis_menubar_integration);
##			
##				foreach($newvalue as $plugin)
##						if($plugin!=$thesis_menubar_integration) $new_active_plugins[] = $plugin;
##	
##			
##				return $new_active_plugins;
##			}
##		
##			function auto_hook(){
##				if (function_exists('openhook_after_header')) {
##					if(get_option('openhook_before_header_nav_menu')) { // Open Hook has moved thesis_nav_menu. 
##						return; // Don't run normal hook filter.
##					}
##				}
##				
##				// If OpenHook hasn't moved thesis_nav_menu, check normal hooks for its location & replace is with pd_nav_menu
##				global $wp_filter;
##				
##				$old_function = 'thesis_nav_menu';
##				$new_function = 'pd_nav_menu';
##				$hook_path = multi_array_key_exists($old_function, $wp_filter);
##				
##				$h = explode(':', $hook_path); // 0:hook, 1:priority, 2:function
##				$hook 		=	$h[0];
##				$priority	=	$h[1];
##				#$function	=	$h[2];
##				
##				remove_action($hook, $old_function, $priority);
##				add_action($hook, $new_function, $priority); // Lose nice colors... Gain editability
##			}
##			
##		}
##	
##		class PdNav {
##		
##			public $centered		=	false;
##			public $padding			=	'7,9,7,9';
##			public $width 			=	980;
##			public $border			=	0;
##			public $menu_id			=	'thesis-nav';
##			public $wrap_id			=	'thesis_nav_wrap';
##			public $use_thesis_nav	=	false;
##			public $override_width	=	array();
##			public $html 			=	null;
##		
##		
##			public function menu_centered() {
##				$this->$centered_tabs = true;
##				$this->menu();
##			}
##		
##			// Replace normal navigation with WP_Nav Plugin generated Menu
##			public function output() {
##				// Check to see if WP_Menubar is installed
##				$plugins = get_option('active_plugins');
##			    $required_plugin = 'menubar/wpm-main.php';
##			    if ( !in_array( $required_plugin , $plugins ) ) {
##			    	echo('Thesis/Menubar Integration Failed. Please install & activate the <a href="http://wordpress.org/extend/plugins/menubar/">Menubar Plugin</a> and <a href="http://www.dontdream.it/download?dl_cat=2">SuckerFish or SuperFish template</a>.');
##					return false;
##			    }
##	
##				// Catch output of Menubar Plugin or Thesis Menubar
##				ob_start();
##				if ($this->use_thesis_nav === false) {
##					do_action('wp_menubar',$this->menu_id);
##				}else {
##					thesis_nav_menu();
##				}
##				$menu = ob_get_contents();
##				ob_end_clean();
##	
##				// Check for WP Menubar error
##				if(strpos($menu, 'WP Menubar error') !== false) {
##					$error = strip_tags($menu);
##	
##					// Maybe they used an underscore instead of a hyphen...
##					ob_start();
##					do_action('wp_menubar',str_replace('-', '_', $this->menu_id));
##					$menu = ob_get_contents();
##					ob_end_clean();
##				
##					// Still Doesn't work? Tell them so.
##					if(strpos($menu, 'WP Menubar error') !== false) {
##						echo $error;
##						return false;
##					}
##				}
##	
##				// Wrapper
##				$menu = '<div id="'.$this->wrap_id.'">'.$menu.'</div>';
##	
##				// Load DOM
##				if (class_exists('phpQuery')) {
##					$this->html = phpQuery::newDocument($menu);
##					#$this->html = new simple_html_dom;
##					#$this->html->load($menu);
##				}else {
##					exit('Could not load phpQuery');
##				}
##				
##				// Assign Thesis-style classes to Menubar
##				if ($this->use_thesis_nav === false) {
##					$this->add_classes();
##				}
##				
##				if ($this->centered === true) {
##					$this->centered_tabs($top_tabs);
##				}
##				
##				do_action('pd_nav_hook_before_output');
##				
##				print $this->html;
##			}
##		
##			private function add_classes($top_tabs) {
##				if ( empty($this->html['ul']->elements) ) {
##					echo('Please populate menu "'.$this->menu_id.'"');
##					return false;
##				}
##				
##				$this->html['ul:first']
##					->addClass('menu')
##					->children('li')
##						->addClass('tab')
##						->children('ul')
##							->addClass('children')
##							->end()
##						->filter('.selected')
##							->addClass('current');
##				
##				$this->html['li.current-cat']
##					->parents('li')
##						->addClass('current-cat-parent');
##						
##				$this->html['li.current_page_item']
##					->addClass('current')
##						->parents('li')
##							->addClass('current-parent');
##							
##				/* 
##				// Verify parent <ul>
##				$ul_first = $this->html->find('ul',0);
##			
##				if(!is_object($ul_first)) {
##					echo('Please populate menu "'.$this->menu_id.'"');
##					return false;
##				}
##	
##				// Assign .menu to parent <ul>
##				$ul_first->class = trim($ul_first->class.' menu');
##	
##				foreach($top_tabs as $key => $tab) {
##					if(is_object($tab)) {
##						// Add .tab to top level <li>'s
##						$tab->class = trim($tab->class.' tab');
##				
##						// Add .children to sub-<ul>'s
##						$ul = $tab->find('ul',0);
##						if (is_object($ul)) {
##							$ul->class = trim($ul->class.' children');
##						}
##				
##						## Detect active sub-categories
##						$current_cat = $tab->find('li.current-cat', 0);
##						if (is_object($current_cat)) {
##							$tab->class = $tab->class.' current-cat-parent';
##							$on_sub_item = true;
##						}
##	
##						## Detect active sub-pages
##						$current_page = $tab->find('li.current_page_item', 0);
##						if (is_object($current_page)) {
##							$tab->class = $tab->class.' current-parent';
##							$current_page->class = $current_page->class.' current';
##							$on_sub_item = true;
##						}
##				
##						## Detect active Tab
##						if (!$on_sub_item && strpos($tab->class, 'selected') !== false) {
##							$tab->class = $tab->class.' current';
##						}
##				
##					}
##				} */
##			}
##		
##			private function centered_tabs($top_tabs) {
##				$superfish = ( !empty( $this->html['ul.Superfish']->elements ) ) ? true : false;
##	
##				$top_tabs = $this->html['ul:first > li'];
##				foreach ($top_tabs as $key => $tab) {
##					$tab_len[$key] = (int) strlen( pq($tab)->find('a:first')->text() );
##					
##					// Give a little more padding to tabs with children if we're using SuperFish
##					// (Superfish can add ">>" to parents via javascript)
##					if( !empty(pq($tab)->find('ul')->elements) && $superfish === true) {
##						$tab_len[$key] = $tab_len[$key]+2; // Expect a space and arrow = two more characters
##					}
##				}
##				
##				$padding = explode(',', $this->padding);
##				foreach ($padding as $key => $val) {
##					$pad_style .= $val.'px ';
##				}
##				
##				// Give less weight to over 12 characters, set minimum at 5
##				foreach ($tab_len as $key => &$len) {
##					if ($len > 12) {
##						$len = ($len - 12)*.15 + 12;
##					}
##					if ($len < 5) {
##						$len = 5;
##					}
##				}
##	
##				// Change characters to pixels based on $len to $tab_tot ratio
##				$tab_tot = array_sum($tab_len);
##	
##				foreach ($tab_len as $key => &$len) {
##					$len = floor($len/$tab_tot*$this->width);
##				}
##			
##				if (!empty($this->override_width)) {
##					$tab_len = $this->override_width($tab_len);
##				}
##			
##				// Account for pixels lost in floor()
##				$leftover = $this->width-array_sum($tab_len);
##				$tab_count = count($tab_len);
##	
##				for($i=0;$i<$leftover;$i++){
##					$c = ($c < $tab_count) ? $c+1 : 0;
##					$tab_len[$c]++;
##				}
##			
##			
##			
##				// Apply styles, account for border and padding
##				foreach($top_tabs as $key => $tab) {
##					pq($tab)->find('a:first')
##						->attr('style', 'width:'.($tab_len[$key]-$padding[1]-$padding[3]-$this->border*2).'px; padding:'.$pad_style.';text-align:center');
##				}
##				
##				
##				/*
##				// Are we using the SuperFish template?
##				$superfish = (is_object($this->html->find('ul.Superfish',0))) ? true : false;
##			
##				foreach($top_tabs as $key => $tab) {
##					$tab_a = $tab->find('a',0);
##					$tab_len[$key] = (int) strlen($tab_a->plaintext);
##				
##					// Give a little more padding to tabs with children if we're using SuperFish
##					// (Superfish can add ">>" to parents via javascript)
##					if(is_object($tab->find('ul',0)) && $superfish === true) {
##						$tab_len[$key] = $tab_len[$key]+2; // Expect a space and arrow = two more characters
##					}
##				}
##			
##				$padding = explode(',', $this->padding);
##				foreach ($padding as $key => $val) {
##					$pad_style .= $val.'px ';
##				}
##			
##				// Give less weight to over 12 characters, set minimum at 5
##				foreach ($tab_len as $key => &$len) {
##					if ($len > 12) {
##						$len = ($len - 12)*.15 + 12;
##					}
##					if ($len < 5) {
##						$len = 5;
##					}
##				}
##	
##				// Change characters to pixels based on $len to $tab_tot ratio
##				$tab_tot = array_sum($tab_len);
##	
##				foreach ($tab_len as $key => &$len) {
##					$len = floor($len/$tab_tot*$this->width);
##				}
##			
##				if (!empty($this->override_width)) {
##					$tab_len = $this->override_width($tab_len);
##				}
##			
##				// Account for pixels lost in floor()
##				$leftover = $this->width-array_sum($tab_len);
##				$tab_count = count($tab_len);
##	
##				for($i=0;$i<$leftover;$i++){
##					$c = ($c < $tab_count) ? $c+1 : 0;
##					$tab_len[$c]++;
##				}
##			
##			
##			
##				// Apply styles, account for border and padding
##				foreach($top_tabs as $key => $tab) {
##					if(is_object($tab)) {
##						$tab_a = $tab->find('a',0);
##						$tab_a->style = 'width:'.($tab_len[$key]-$padding[1]-$padding[3]-$this->border*2).'px; padding:'.$pad_style.';text-align:center';
##					}
##				}
##				*/
##			}
##		
##			private function override_width($tab_len) {
##				foreach ($this->override_width as $tab_index => $w_new) {
##					$tab_index = $tab_index;
##					$w_new = $w_new;
##					break;
##					// There should only be one override coming through. take the first.
##				}
##			
##				// Calculate the difference between the old and new width
##				$w_old = $tab_len[$tab_index];
##				$delta = $w_old - $w_new;
##			
##				// Calculate the value to distribute the change equally among the other tabs
##				$count = count($tab_len);
##				$distribute = floor($delta/($count-1));
##			
##			
##				// Make the changes
##				foreach($tab_len as $key => &$val) {
##					if ($key !== $tab_index) {
##						$val += $distribute;
##					}else {
##						$val = $w_new;
##					}
##				}
##	
##				return $tab_len;
##			}
##		}
##	
##	#}end Classes
##	
##	#  ==========================
##	#	Hooks
##	#  ========================={
##	
##		// Auto-detect what hook thesis_nav_menu is on and replace it with pd_nav_menu
##		add_action('template_redirect', array('ThesisMenubarIntegration','auto_hook'),1);
##		
##		// Plugin activation
##		register_activation_hook(__FILE__, array('ThesisMenubarIntegration','activation_hook'));
##		register_deactivation_hook(__FILE__, array('ThesisMenubarIntegration','deactivation_hook'));
##		
##	#}end Hooks

?>