<?php
/**
 * PDStyles class for admin actions
 * 
 * This class contains all functions and actions required for PDStyles to work in the admin of WordPress
 * 
 * @since 0.1
 * @package pd-styles
 * @subpackage Admin
 * @author pdclark
 **/
class PDStylesAdminController extends PDStyles {
	
	/**
	 * Full file system path to the main plugin file
	 *
	 * @since 0.1
	 * @var string
	 */
	var $plugin_file;

	/**
	 * Path to the main plugin file relative to WP_CONTENT_DIR/plugins
	 *
	 * @since 0.1
	 * @var string
	 */
	var $plugin_basename;

	/**
	 * Name of options page hook
	 *
	 * @since 0.1
	 * @var string
	 */
	var $options_page_hookname;
	
	/**
	 * Variables loaded from CSS @variables declarations
	 * 
	 * @since 0.1
	 * @var array
	 **/
	var $css_variables;
	
	/**
	 * Container for CSS Scaffold object
	 * 
	 * @since 0.1
	 * @var object
	 **/
	var $scaffold;
	
	/**
	 * Hold extension objects
	 * 
	 * @since 0.1
	 * @var object
	 **/
	var $extensions;
	
	/**
	 * Setup backend functionality in WordPress
	 *
	 * @return none
	 * @since 0.1
	 */
	function __construct() {
		parent::__construct();
		$this->options = get_option( 'pd-styles' );

		if ( version_compare ( $this->get_option ( 'version' ) , $this->dbversion , '!=' ) && ! empty ( $this->options ) ) {
			$this->check_upgrade();
		}
		
		// Load localizations if available
		// load_plugin_textdomain ( 'shadowbox-js' , false , 'shadowbox-js/localization' );
        
		// Activation hook
		register_activation_hook ( $this->plugin_file , array( &$this , 'init' ) );
        
		// Whitelist options
		add_action ( 'admin_init' , array( &$this , 'register_settings' ) );
        
		// Activate the options page
		add_action ( 'admin_menu' , array( &$this , 'add_page' ) ) ;
        
		// Full path and plugin basename of the main plugin file
		$this->plugin_file = dirname ( dirname ( dirname ( __FILE__ ) ) ) . '/pd-styles.php';
		$this->plugin_basename = plugin_basename ( $this->plugin_file );
		
	}
	
	/**
	 * Load CSS variables, extensions, and scaffold objects
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function build() {
		
		$this->load_extensions( $this->plugin_dir_path() . 'extensions' );

	}
	
	/**
	 * Loads the extension files
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function load_extensions( $path /*,$scaffold*/)
	{	
		# Scaffold_Helper object
		// $helper = $this->getHelper();
		
		# The glob path
		$path = realpath($path) . DIRECTORY_SEPARATOR . '*';
	
		# Load each of the extensions
		foreach(glob($path) as $ext)
		{			
			$ext .= DIRECTORY_SEPARATOR;
		
			$config 	= array();
			$name 		= basename($ext);
			$class 		= 'PDStyles_Extension_' . $name;
			$file 		= $ext.$name.'.php';
			
			# This extension isn't enabled
			//if(!in_array($name, $this->options['extensions']))
			//	continue;
			
			# Get the config for the extension if available
			//if(isset($this->options[$name]))
			//	$config = $this->options[$name];
			
			# Load the controller
			if(file_exists($file))
			{
				require_once realpath($ext.$name.'.php');
				$object = new $class($config);
				// $object->attach_helper($helper);
				$this->attach($name,$object);
			}
		}
		// return $scaffold;
	}
	
	/**
	 * Whitelist the pd-styles options
	 *
	 * @since 0.1
	 * @return none
	 */
	function register_settings() {
		register_setting ( 'pd-styles' , 'pd-styles' , array( &$this , 'update' ) );
	}
	
	/**
	 * Enqueue javascript required for the admin settings page
	 * 
	 * @return none
	 * @since 0.1
	 */
	function admin_js() {
		wp_enqueue_script ( 'jquery' );
		
		wp_register_script('pds-colorpicker', $this->plugin_url().'/lib/js/colorpicker/js/colorpicker.js',array('jquery'), $this->version, true);
		
		wp_enqueue_script('pds-admin-main', $this->plugin_url().'/lib/js/admin-main.js',array('jquery', 'pds-colorpicker'), $this->version, true);

		
		
		// wp_enqueue_script ( 'shadowbox-js-helper' , $this->plugin_url() . '/js/shadowbox-admin-helper.js' , array( 'jquery' ) , $this->version , true );
		
		/*
		// See http://www.prelovac.com/vladimir/best-practice-for-adding-javascript-code-to-wordpress-plugin
		wp_localize_script ( 'shadowbox-js-helper' , 'shadowboxJsHelperL10n' , array(
			'advConfShow'	 => __( 'Show Advanced Configuration' , 'shadowbox-js' ) ,
			'advConfHide'	 => __( 'Hide Advanced Configuration' , 'shadowbox-js' ) ,
			'messageConfirm' => __( 'Do you agree that you are not using FLV support for commercial purposes or have already purchased a license for JW FLV Media Player?' , 'shadowbox-js' )
		) );
		*/
	}
	
	/**
	 * Enqueue CSS required for the admin settings page
	 *
	 * @return none
	 * @since 0.1
	 */
	function admin_css() {
		
		wp_register_style('pds-colorpicker', $this->plugin_url().'/lib/js/colorpicker/css/colorpicker.css',array( ), $this->version);
		
		wp_enqueue_style ( 'pd-styles-admin-css' , apply_filters ( 'pd-styles-admin-css' , '/?scaffold&file=lib/css/admin.css' ) , array('pds-colorpicker') , $this->version , 'screen' );
		wp_enqueue_style ( 'pd-styles-admin-css-test' , '/?scaffold&file=example/vars.css' , array() , $this->version , 'screen' );
	}
	
	/**
	 * Return a list of the languages available to pd-styles.js
	 *
	 * @author Matt Martz <matt@sivel.net>
	 * @since 2.0.3.3
	 * @return array
	 */
	function languages() {
		$languages = array( 
			'ar' ,    // Arabic
			'ca' ,    // Catalan
			'cs' ,    // Czech
			'da' ,    // Danish
			'de-CH' , // Swiss German
			'de-DE' , // German
			'en' ,    // English
			'es' ,    // Spanish
			'et' ,    // Estonian
			'fi' ,    // Finnish
			'fr' ,    // French
			'gl' ,    // Galician 
			'he' ,    // Hebrew
			'hu' ,    // Hungarian
			'id' ,    // Indonesian
			'is' ,    // Icelandic
			'it' ,    // Italian
			'ja' ,    // Japanese
			'ko' ,    // Korean
			'my' ,    // Burmese 
			'nl' ,    // Dutch
			'no' ,    // Norwegian
			'pl' ,    // Polish
			'pt-BR' , // Brazilian Portuguese
			'pt-PT' , // Portuguese
			'ro' ,    // Romanian
			'ru' ,    // Rusian
			'sk' ,    // Slovak
			'sv' ,    // Swedish
			'tr' ,    // Turkish
			'zh-CN' , // Chinese (Simplified)
			'zh-TW'   // Chinese (Traditional)
		);
		return $languages;
	}
	
	/**
	 * Try to set pdstyles language based on defined language for WordPress
	 *
	 * @author Matt Martz <matt@sivel.net>
	 * @since 0.1
	 * @return string
	 */
	function set_lang() {
		if ( defined ( 'WPLANG' ) )
			$wp_lang = WPLANG;
		else
			$wp_lang = 'en';
	
		switch ( $wp_lang ) {
			case 'ar' :
				$lang = 'ar';    // Arabic
				break;
			case 'ca' :
				$lang = 'ca';    // Catalan
				break;
			case 'cs_CZ' :
				$lang = 'cs';    // Czech
				break;
			case 'da_DK' :
				$lang = 'da';    // Danish
				break;
			case 'de_DE' :
				$lang = 'de-DE'; // German
				break;
			case 'es_ES' :
				$lang = 'es';    // Spanish
				break;
			case 'et' :
				$lang = 'et';    // Estonian
				break;
			case 'fi' :
			case 'fi_FI':
				$lang = 'fi';    // Finnish
				break;
			case 'fr_BE' :
			case 'fr_FR' :
				$lang = 'fr';    // French
				break;
			case 'gl_ES' :
				$lang = 'gl';    // Galician
				break;
			case 'he_IL' :
				$lang = 'he';    // Hebrew
				break;
			case 'hu_HU' :
				$lang = 'hu';    // Hungarian
				break;
			case 'id_ID' :
				$lang = 'id';    // Indonesian
				break;
			case 'is_IS' :
				$lang = 'is';    // Icelandic
				break;
			case 'it_IT' :
				$lang = 'it';    // Italian
				break;
			case 'ja' :
				$lang = 'ja';    // Japanese
				break;
			case 'ko_KR' :
				$lang = 'ko';    // Korean
				break;
			case 'my_MM' :
				$lang = 'my';    // Burmese
				break;
			case 'nl' :
			case 'nl_NL' :
				$lang = 'nl';    // Dutch
				break;
			case 'nn_NO' :
				$lang = 'no';    // Norwegian
				break;
			case 'pl_PL' :
				$lang = 'pl';    // Polish
				break;
			case 'pt_BR' :
				$lang = 'pt-BR'; // Brazilian Portuguese
				break;
			case 'pt_PT' :
				$lang = 'pt-PT'; // Portuguese
				break;
			case 'ro' :
				$lang = 'ro';    // Romanian
				break;
			case 'ru_RU' :
			case 'ru_UA' :
				$lang = 'ru';    // Rusian
				break;
			case 'sk' :
				$lang = 'sk';    // Slovak
				break;
			case 'sv_SE' :
				$lang = 'sv';    // Swedish
				break;
			case 'tr' :
				$lang = 'tr';    // Turkish
				break;
			case 'zh_CN' :
				$lang = 'zh-CN'; // Chinese (Simplified)
				break;
			default :
				$lang = 'en';    // English
				break;
		}
		return $lang;
	}

	/**
	 * Return the default options
	 *
	 * @return array
	 * @since 0.1
	 */
	function defaults() {
		$defaults = array(
			'version'           => $this->db_version ,
			'language'          => $this->set_lang() ,
			'css_values'		=> array(),
			'scaffold'			=> array(
				// See scaffold/parse.php for full scaffold config documentation
				'config'		=> array(
					'production'			=> false,
					'max_age'				=> false,
					'output_compression'	=> false,
					'set_etag'				=> true,
					'enable_string'			=> false,
					'enable_url'			=> false,
					'load_paths'			=> array(
						untrailingslashit( get_stylesheet_directory() ),
						untrailingslashit( $this->plugin_dir_path() ),
					),
					'extensions'			=> array(
						'AbsoluteUrls',
						'Embed',
						'Functions',
						//'HSL',
						'ImageReplace',
						// 'Minify',
						'Properties',
						'Random',
						'Import',
						'Mixins',
						'NestedSelectors',
						//'XMLVariables',
						'Variables',
						'PDStyles',
                	
						# Process-heavy Extensions
						//'Sass',
						//'CSSTidy',
						//'YUI'
					),
				), // end config
			), // end scaffold
			
		);
		return $defaults;
	}
	
	/**
	 * Initialize the default options during plugin activation
	 *
	 * @return none
	 * @since 0.1
	 */
	function init() {
		if ( ! get_option ( 'pd-styles' ) ) {
			$this->options = $this->defaults();
			add_option ( 'pd-styles' , $this->options );
		} else {
			$this->check_upgrade();
		}
		// $this->build_shadowbox ( true ); // Attempt to build and cache shadowbox.js
	}
	
	/**
	 * Check if an upgraded is needed
	 *
	 * @return none
	 * @since 0.1
	 */
	function check_upgrade() {
		/*
		if ( $this->version_compare ( array( '3.0.0.0' => '<' ) ) )
			$this->upgrade ( '3.0.0.0' );
		else if ( $this->version_compare ( array( '3.0.0.0' => '>' , '3.0.0.2' => '<' ) ) )
			$this->upgrade ( '3.0.0.2' );
		else if ( $this->version_compare ( array( '3.0.0.2' => '>' , '3.0.3' => '<' ) ) )
			$this->upgrade ( '3.0.3' );
		*/
	}
	
	/**
	 * Compare Versions
	 *
	 * @param array Array of the version you want to compare to the version stored in the database as the key and the operator as the value
	 * @return bool
	 * @since 0.1
	 */
	function version_compare ( $versions ) {
		foreach ( $versions as $version => $operator ) {
			if ( version_compare ( $this->get_option ( 'version' ) , $version , $operator ) )
				$response = true;
			else
				$response = false; 
		}
		return $response;
	}
	
	/**
	 * Upgrade options 
	 *
	 * @return none
	 * @since 0.1
	 */
	function upgrade ( $ver ) {
		/*
		if ( $ver == '3.0.0.0' ) { // Upgrades for versions below 3.0.0.0
			$newopts = array(
				'version'           => '3.0.0.0' ,
				'smartLoad'         => 'false' ,
				'enableFlv'         => 'false' ,
				'tubeWidth'         => 640 ,
				'tubeHeight'        => 385 ,
				'players'           => $this->players() ,
				'autoDimensions'    => 'false' ,
				'showOverlay'       => 'true' ,
				'skipSetup'         => 'false' ,
				'flashParams'       => '{bgcolor:"#000000", allowFullScreen:true}' , 
				'flashVars'         => '{}' ,
				'flashVersion'      => '9.0.0'
			);
			unset ( $this->options['ie8hack'] , $this->options['skin'] );
			$this->options = array_merge ( $this->options , $newopts );
			update_option ( 'shadowbox' , $this->options );
		} else if ( $ver == '3.0.0.2' ) { // Upgrades for versions below 3.0.0.2
			$newopts = array( 
				'version'           => '3.0.0.2' ,
				'useSizzle'         => 'false' ,
				'genericVideoHeight'=> $this->options['tubeHeight'] ,
				'genericVideoWidth' => $this->options['tubeWidth']
			);
			if ( $this->options['enableFlv'] == 'true' )
				$newopts['autoflv'] = 'true';
			else
				$newopts['autoflv'] = 'false';
			unset ( $this->options['tubeHeight'] , $this->options['tubeWidth'] );
			$this->options = array_merge ( $this->options , $newopts );
			update_option ( 'shadowbox' , $this->options );
		} else if ( $ver == '3.0.3' ) { // Upgrades for versions below 3.0.3
			$this->options['version'] = '3.0.3';
			if ( in_array( $this->options['library'] , array( 'ext' , 'dojo') ) )
				$this->options['library'] = 'base';
			update_option ( 'shadowbox', $this->options );
		}
		*/
		$this->check_upgrade();
	}
	
	/**
	 * Update/validate the options in the options table from the POST
	 *
	 * @since 0.1
	 * @return none
	 */
	function update ( $options ) {
		// Make sure there are no empty values, seems users like to clear out options before saving
		foreach ( $this->defaults() as $key => $value ) {
			if ( 
				( ! isset ( $options[$key] ) || empty ( $options[$key] ) ) 
				&& $key != 'delete' 
				&& $key != 'default'
			) {
				$options[$key] = $value;
			}
		}
		
		// Validate CSS vars
		foreach( $options['css_values'] as $file => &$groups ) {
			foreach ( $groups as $group => &$variables ) {
				foreach ( $variables as $key => &$value ) {
					
					switch( get_class( $this->css_variables[$group][$key] ) ) {
						case 'PDStylesUIColor':
							$value = '#'.trim( $value, '# ');
							// $this->css_variables[$group][$key] = $value;
							break;
						
					}
	
				}
			}
		}
		
		// Check if we are supposed to remove options
		if ( isset ( $options['delete'] ) && $options['delete'] == 'true' ) { 
			delete_option ( 'pd-styles' );
		} else if ( isset ( $options['default'] ) && $options['default'] == 'true' ) { // Check if we are supposed to reset to defaults
			$this->options = $this->defaults();
			// $this->build_shadowbox ( true ); // Attempt to build and cache shadowbox.js
			return $this->options;
		} else {
			// Save options
			unset ( 
				$options['delete'], 
				$options['default'],
				$options['_wpnonce'],
				$options['_wp_http_referer'],
				$options['action']
			);
			$this->options = $options;
			// $this->build_shadowbox ( true ); // Attempt to build and cache shadowbox.js
			return $this->options;
		}
	}
	
	// !!! This could be swapped out for build_contstants_xml
	// !!! Note the optional AJAX output for possible JS or CSS use
	/**
	 * Build the JS output for shadowbox.js
	 *
	 * Shadowbox.js is now built in a very specific order,
	 * so to dynamically load what we want, we need to build
	 * the JavaScript dynamically, this causes issues with
	 * determining the path to shadowbox.js also, so we have to
	 * do some hacks further down too.
	 *
	 * @since 3.0.3
	 * @param $tofile Boolean write output to file instead of echoing
	 * @return none
	 */
	/*
	function build_shadowbox ( $tofile = false ) {
		// If the user is filtering the url for shadowbox.js just bail out here
		if ( has_filter ( 'shadowbox-js' ) )
			return;

		$plugin_url = $this->plugin_url();
		$plugin_dir = WP_PLUGIN_DIR . '/' . dirname ( $this->plugin_basename );

		// Ouput correct content-type, and caching headers
		if ( ! $tofile )
			cache_javascript_headers();

		$output = '';

		// Start build
		foreach ( array( 'intro' , 'core' , 'util' ) as $include ) {
			// Replace S.path with the correct path, so we don't have to rely on autodetection which is broken with this method
			if ( $include == 'core' )
				$output .= str_replace ( 'S.path=null;' , "S.path='$plugin_url/shadowbox/';" , file_get_contents ( "$plugin_dir/shadowbox/$include.js" ) );
			else
				$output .= file_get_contents ( "$plugin_dir/shadowbox/$include.js" );
		}

		$library = $this->get_option ( 'library' );
		$output .= file_get_contents ( "$plugin_dir/shadowbox/adapters/$library.js" );

		foreach ( array( 'load' , 'plugins' , 'cache' ) as $include )
			$output .= file_get_contents ( "$plugin_dir/shadowbox/$include.js" );

		if ( $this->get_option ( 'useSizzle' ) == 'true' && $this->get_option ( 'library' ) != 'jquery' )
			$output .= file_get_contents ( "$plugin_dir/shadowbox/find.js" );

		$players = $this->get_option ( 'players' );
		if ( in_array( 'flv' , $players ) || in_array( 'swf' , $players ) )
			$output .= file_get_contents ( "$plugin_dir/shadowbox/flash.js" );

		$language = $this->get_option ( 'language' );
		$output .= file_get_contents ( "$plugin_dir/shadowbox/languages/$language.js" );

		foreach ( $players as $player )
			$output .= file_get_contents ( "$plugin_dir/shadowbox/players/$player.js" );

		foreach ( array( 'skin' , 'outro' ) as $include )
			$output .= file_get_contents ( "$plugin_dir/shadowbox/$include.js" );

		// if we are supposed to write to a file then do so
		if ( $tofile ) {
				$upload_dir = wp_upload_dir();
				$shadowbox_dir = "{$upload_dir['basedir']}/shadowbox-js/";
				$shadowbox_file = $shadowbox_dir . $this->md5() . '.js';

				if ( ! is_dir ( $shadowbox_dir ) && is_writable ( $upload_dir['basedir'] ) )
					wp_mkdir_p ( $shadowbox_dir );

				if ( ! file_exists ( $shadowbox_file ) && is_dir ( $shadowbox_dir ) && is_writable ( $shadowbox_dir ) ) {
					$fh = fopen ( $shadowbox_file, 'w+' );
					fwrite ( $fh , $output );
					fclose ( $fh );
				}
		} else { // otherwise just echo (backup call to admin-ajax.php for on the fly building)
			echo $output;
			die();
		}
	}
	*/
	
	/**
	 * Add the options page
	 *
	 * @return none
	 * @since 0.1
	 */
	function add_page() {
		if ( current_user_can ( 'manage_options' ) ) {
			$this->options_page_hookname = add_theme_page ( __( 'Styles' , 'pd-styles' ) , __( 'Styles' , 'pd-styles' ) , 'manage_options' , 'pd-styles' , array( &$this , 'admin_page' ) );
			add_action ( "admin_print_scripts-{$this->options_page_hookname}" , array( &$this , 'admin_js' ) );
			add_action ( "admin_print_styles-{$this->options_page_hookname}" , array( &$this , 'admin_css' ) );
			add_filter ( "plugin_action_links_{$this->plugin_basename}" , array( &$this , 'filter_plugin_actions' ) );

			add_action ( "load-{$this->options_page_hookname}" , array( &$this , 'build' ) );
		}
	}
	
	/**
	 * Add a settings link to the plugin actions
	 *
	 * @param array $links Array of the plugin action links
	 * @return array
	 * @since 0.1
	 */
	function filter_plugin_actions ( $links ) { 
		$settings_link = '<a href="themes.php?page=pd-styles">' . __( 'Settings' , 'pd-styles' ) . '</a>'; 
		array_unshift ( $links, $settings_link ); 
		return $links;
	}
	
	/**
	 * Output the options page
	 *
	 * @return none
	 * @since 0.1
	 */
	function admin_page() {
		$this->load_view('admin-main.php');
	}

} // END class PDStylesAdminController extends PDStyles

?>