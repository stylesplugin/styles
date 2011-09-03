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
class PDStylesAdmin extends PDStyles {
	
	/**
	 * Name of options page hook
	 *
	 * @since 0.1
	 * @var string
	 */
	var $options_page_hookname;
	
	/**
	 * Setup backend functionality in WordPress
	 *
	 * @return none
	 * @since 0.1
	 */
	function __construct( $args = array() ) {
		parent::__construct();
		
		$defaults = array(
			'file'	=> apply_filters( 'pdstyles_default_file', $this->plugin_dir_path() . 'example/vars.scss' ),
		);
		$args = wp_parse_args( $args, $defaults );
		
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
        
		// AJAX
		add_action('wp_ajax_pdstyles-update-options', array( &$this, 'update_ajax') );
		add_action('wp_ajax_pdstyles-frontend-load', array( &$this, 'ajax_frontend_load') );
	}
	
	/**
	 * Whitelist the pd-styles options
	 *
	 * @since 0.1
	 * @return none
	 */
	function register_settings() {
		register_setting ( 'pd-styles' , 'pd-styles' , array( &$this , 'update' ) ); // update = validation method
		register_setting ( 'pd-styles' , 'pd-styles-preview' , array( &$this , 'update_preview' ) );
		
		register_setting(
            'Demo_Vars_Group', 
            'Demo_Vars', 
            array('DemoPlugin', 'Validate'));
 
        add_settings_section(
            'Demo_Vars_ID', 
            'Demo Vars Title', 
            array('DemoPlugin', 'Overview'), 
            'PDStyles_Settings');
 
        add_settings_field(
            'Control_ID',                         // Unique ID
            'Demo Control Name',                  // Label
            array('DemoPlugin', 'Demo_Control'),  // Display callback
            'PDStyles_Settings',                  // Form page
            'Demo_Vars_ID'                        // Form section
		); 
	}
	
	/**
	 * Enqueue javascript required for the admin settings page
	 * 
	 * @return none
	 * @since 0.1
	 */
	function admin_js() {
		
		wp_enqueue_script('postbox');
		wp_enqueue_script('dashboard');
		
		wp_enqueue_script('pds-admin-main', $this->plugin_url().'/js/admin-main.js', array('jqcookie', 'jquery', 'pds-colorpicker', 'thickbox', 'media-upload' ), $this->version, true);

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
		
		wp_enqueue_style('dashboard');
		wp_enqueue_style('thickbox');
		wp_enqueue_style('global');
		wp_enqueue_style('wp-admin');
		
		wp_register_style('pds-colorpicker', $this->plugin_url().'/js/colorpicker/css/colorpicker.css',array( ), $this->version);
		
		wp_register_style('pds-slider', $this->plugin_url().'/css/ui-lightness/jquery-ui-1.8.6.custom.css',array( ), $this->version);
		wp_enqueue_style('pds-slider');
		
		wp_enqueue_style ( 'pd-styles-admin' , apply_filters ( 'pd-styles-admin-css' , '/?scaffold&file=css/admin.css' ) , array('pds-colorpicker') , $this->version , 'screen' );
		// wp_enqueue_style ( 'pd-styles-admin-test' , $this->plugin_dir_path() . 'example/vars.scss' , array() , $this->version , 'screen' );
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
	function upgrade( $ver ) {
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
	function update( $input ) {
		if ( !is_object($this->variables) ) $this->build();
		
		// Make sure there are no empty values, seems users like to clear out options before saving
		foreach ( $this->defaults() as $key => $value ) {
			if ( 
				( ! isset ( $input[$key] ) || empty ( $input[$key] ) ) 
				&& $key != 'delete' 
				&& $key != 'default'
			) {
				$input[$key] = $value;
			}
		}

		// Update vars in active file object
		$this->files->active_file->set( $input['variables'] );
		// Convert input array to object for storage
		$input['variables'][ $this->permalink ] = $this->files->active_file;
		
		// Check if we are supposed to remove options
		if ( isset ( $input['delete'] ) && $input['delete'] == 'true' ) { 
			delete_option ( 'pd-styles' );
		} else if ( isset ( $input['default'] ) && $input['default'] == 'true' ) { // Check if we are supposed to reset to defaults
			$this->options = $this->defaults();
			return $this->options;
		} else {
			// Save options
			unset ( 
				$input['delete'], 
				$input['default'],
				$input['_wpnonce'],
				$input['_wp_http_referer'],
				$input['action']
			);
			
			// Update current object for further processing
			$this->options = $input; 
			
			// Strip Scaffold from object saved to DB
			$input['variables'][ $this->permalink ]->db_cleanup();
			
			// Write to DB
			return $input; 
		}
	}
	
	/**
	 * Update CSS variables used for preview CSS
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function update_preview( $input ) {
		$this->build();

		// Update vars in active file object
		$this->files->active_file->set( $input['variables'] );
		// Convert input array to object for storage
		$input['variables'][ $this->permalink ] = $this->files->active_file;
		
		// Strip Scaffold from object saved to DB
		$input['variables'][ $this->permalink ]->db_cleanup();
		
		return $input['variables'];
	}
	
	/**
	 * Handle updating options via AJAX; cache Scaffold output
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function update_ajax() {
		global $blog_id;
		
		$response = array();
		$class = 'updated';
		
		if ( isset( $_POST['preview'] )) {
			
			if ( update_option('pd-styles-preview', $_POST ) ) {
				$response['message'] .= 'Preview variables updated.<br/>';
			}else {
				$response['message'] .= 'Preview variables unchanged.<br/>';
			}
			
		}else {
			if ( update_option('pd-styles', $_POST ) ) {
				$response['message'] .= 'Variables saved.<br/>';
			}else {
				$response['message'] .= 'Variables unchanged.<br/>';
			}
			
			$cache_file = $this->files->active_file->cache_file;
			
			$cache_written = @file_put_contents( $cache_file, $this->render() );
			if ( false !== $cache_written ) {
				$response['message'] .= 'Stylesheet rendered and cached to <code><abbr title="'.$cache_file.'">'.basename($cache_file).'</abbr></code>.<br/>';
			}else {
				$response['message'] = '<div><strong>Error:</strong> Could not write to file <code><abbr title="'.$cache_file.'">'.basename($cache_file).'</abbr></code>.<br/>Please save <a href="/?scaffold">the output</a> manually or make the file writable with: <code>chmod 666 '.$cache_file.'</code></div>';
				$class = 'error';
			}
			
		}
		
		$response['message'] = '<div class="'.$class.' settings-error" id="setting-error-settings_updated"> 
		<p>'.$response['message'].'</p></div>';
		
		$response['href'] = '/?scaffold&preview&time='.microtime(true);
		$response['id'] = $blog_id;
		
		
		
		echo json_encode( $response );

		exit;
		
	}
	
	/**
	 * Serve frontend view via AJAX
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function ajax_frontend_load() {
		$this->build();
		
		$this->load_view('frontend-main.php');
		exit;
	}

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
		// Update options if something was submitted
		if ( $_POST['action'] == 'pdstyles-update-options' && check_admin_referer('pd-styles-update-options') ) {
			// Uses $this->update() sanitation callback
			update_option('pd-styles', $_POST );
		}

		$this->load_view('admin-main.php');
	}

} // END class PDStylesAdminController extends PDStyles

?>