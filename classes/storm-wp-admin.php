<?php
/**
 * StormStyles class for admin actions
 * 
 * This class contains all functions and actions required for StormStyles to work in the admin of WordPress
 * 
 * @since 0.1
 * @package StormStyles
 * @subpackage Admin
 * @author pdclark
 **/
class Storm_WP_Admin extends Storm_WP_Frontend {
	
	/**
	 * Name of options page hook
	 *
	 * @since 0.1
	 * @var string
	 */
	var $options_page_hookname;
	
	/**
	 * Sets up and outputs WP Admin settings fields
	 * 
	 * @var Storm_CSS_Settings
	 **/
	var $admin_settings;
	
	/**
	 * Setup backend functionality in WordPress
	 *
	 * @return none
	 * @since 0.1
	 */
	function __construct( $styles ) {
		parent::__construct( $styles ); // sets $this->styles = $styles

		$defaults = array(
			'file'	=> apply_filters( 'pdstyles_default_file', $this->plugin_dir_path() . 'example/vars.scss' ),
		);
		$args = wp_parse_args( $args, $defaults );
		
		$this->options = get_option( 'styles' );
		
		if ( version_compare ( $this->get_option ( 'version' ), $this->dbversion, '!=' ) && ! empty ( $this->options ) ) {
			$this->check_upgrade();
		}
		
		// Load localizations if available
		// load_plugin_textdomain ( 'styles', false, 'styles/localization' );
        
		// Activation hook
		register_activation_hook ( $this->plugin_file, array( $this, 'init' ) );
        
		// Whitelist options
		add_action ( 'admin_init', array( $this, 'register_settings' ) );
        
		// Activate the options page
		add_action ( 'admin_menu', array( $this, 'add_page' ) ) ;
        
		// AJAX
		add_action('wp_ajax_styles-update-options', array( $this, 'update_ajax') );
		
		// Media Popup Styles
		add_action('admin_print_styles-media-upload-popup', array($this, 'admin_css_media_upload'));
		add_action('admin_print_scripts-media-upload-popup', array($this, 'admin_js_media_upload'));
		
		// Settings page setup
		$this->admin_settings = new Storm_CSS_Settings( $this->styles );
		add_action( 'styles_settings', array($this->admin_settings, 'settings_sections'), 10, $this->styles );
		add_action( 'styles_settings', array($this->admin_settings, 'settings_items'), 20, $this->styles );
		
		// Sanatize before DB commit
		add_filter( 'styles_before_save_element_values', array($this, 'before_save_element_values'), 10 );
		
	}
	
	/**
	 * Whitelist the StormStyles options
	 *
	 * @since 0.1
	 * @return none
	 */
	function register_settings() {
		register_setting ( 'styles', 'styles',         array( $this, 'update' ) ); // update = validation method
		register_setting ( 'styles', 'styles-preview', array( $this, 'update' ) );
	}
	
	/**
	 * Enqueue javascript required for the admin settings page
	 * 
	 * @return none
	 * @since 0.1
	 */
	function admin_js() {
		
		wp_enqueue_script('storm-admin-main');
		
		wp_localize_script ( 'storm-admin-main', 'storm_admin', array(
			'mediaUploadURL'	 => admin_url('media-upload.php'),
			'pluginURL'	 => $this->plugin_url(),
		) );

		/*
		// See http://www.prelovac.com/vladimir/best-practice-for-adding-javascript-code-to-wordpress-plugin
		wp_localize_script ( 'shadowbox-js-helper', 'shadowboxJsHelperL10n', array(
			'advConfShow'	 => __( 'Show Advanced Configuration', 'shadowbox-js' ),
			'advConfHide'	 => __( 'Hide Advanced Configuration', 'shadowbox-js' ),
			'messageConfirm' => __( 'Do you agree that you are not using FLV support for commercial purposes or have already purchased a license for JW FLV Media Player?', 'shadowbox-js' )
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
		
		wp_register_style('storm-colorpicker', $this->plugin_url().'/js/colorpicker/css/colorpicker.css', array(), $this->version);
		wp_register_style('storm-slider', $this->plugin_url().'/css/ui-lightness/jquery-ui-1.8.6.custom.css', array(), $this->version);
		
		wp_enqueue_style ( 'styles-admin', apply_filters ( 'styles-admin-css', $this->plugin_url().'/css/admin.css' ), array('storm-colorpicker', 'storm-slider'), $this->version, 'screen' );
	}
	
	/**
	 * Enqueue CSS for media upload popup when called from Styles page
	 * 
	 * @return void
	 **/
	function admin_css_media_upload() {
		$ref_url = parse_url( $_SERVER['HTTP_REFERER'] );
		parse_str( $ref_url['query'], $ref_get );
		
		if ( $ref_get['page'] !== 'styles' && !isset( $_GET['styles'] ) ) { return; }
		
		wp_enqueue_style ( 'styles-media-upload', $this->plugin_url().'/css/admin-media-upload.css', array(), $this->version );
		
	}
	
	function admin_js_media_upload() {
		wp_enqueue_script ( 'styles-media-upload', $this->plugin_url().'/js/admin-media-upload.js', array('jquery'), $this->version );
	}
	
	/**
	 * Return a list of the languages available to StormStyles.js
	 *
	 * @author Matt Martz <matt@sivel.net>
	 * @since 2.0.3.3
	 * @return array
	 */
	function languages() {
		$languages = array( 
			'ar',    // Arabic
			'ca',    // Catalan
			'cs',    // Czech
			'da',    // Danish
			'de-CH', // Swiss German
			'de-DE', // German
			'en',    // English
			'es',    // Spanish
			'et',    // Estonian
			'fi',    // Finnish
			'fr',    // French
			'gl',    // Galician 
			'he',    // Hebrew
			'hu',    // Hungarian
			'id',    // Indonesian
			'is',    // Icelandic
			'it',    // Italian
			'ja',    // Japanese
			'ko',    // Korean
			'my',    // Burmese 
			'nl',    // Dutch
			'no',    // Norwegian
			'pl',    // Polish
			'pt-BR', // Brazilian Portuguese
			'pt-PT', // Portuguese
			'ro',    // Romanian
			'ru',    // Rusian
			'sk',    // Slovak
			'sv',    // Swedish
			'tr',    // Turkish
			'zh-CN', // Chinese (Simplified)
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
			'version'           => $this->db_version,
			'language'          => $this->set_lang(),
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
		if ( ! get_option ( 'styles' ) ) {
			$this->options = $this->defaults();
			add_option ( 'styles', $this->options );
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
		else if ( $this->version_compare ( array( '3.0.0.0' => '>', '3.0.0.2' => '<' ) ) )
			$this->upgrade ( '3.0.0.2' );
		else if ( $this->version_compare ( array( '3.0.0.2' => '>', '3.0.3' => '<' ) ) )
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
			if ( version_compare ( $this->get_option ( 'version' ), $version, $operator ) )
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
				'version'           => '3.0.0.0',
				'smartLoad'         => 'false',
				'enableFlv'         => 'false',
				'tubeWidth'         => 640,
				'tubeHeight'        => 385,
				'players'           => $this->players(),
				'autoDimensions'    => 'false',
				'showOverlay'       => 'true',
				'skipSetup'         => 'false',
				'flashParams'       => '{bgcolor:"#000000", allowFullScreen:true}', 
				'flashVars'         => '{}',
				'flashVersion'      => '9.0.0'
			);
			unset ( $this->options['ie8hack'], $this->options['skin'] );
			$this->options = array_merge ( $this->options, $newopts );
			update_option ( 'shadowbox', $this->options );
		} else if ( $ver == '3.0.0.2' ) { // Upgrades for versions below 3.0.0.2
			$newopts = array( 
				'version'           => '3.0.0.2',
				'useSizzle'         => 'false',
				'genericVideoHeight'=> $this->options['tubeHeight'],
				'genericVideoWidth' => $this->options['tubeWidth']
			);
			if ( $this->options['enableFlv'] == 'true' )
				$newopts['autoflv'] = 'true';
			else
				$newopts['autoflv'] = 'false';
			unset ( $this->options['tubeHeight'], $this->options['tubeWidth'] );
			$this->options = array_merge ( $this->options, $newopts );
			update_option ( 'shadowbox', $this->options );
		} else if ( $ver == '3.0.3' ) { // Upgrades for versions below 3.0.3
			$this->options['version'] = '3.0.3';
			if ( in_array( $this->options['library'], array( 'ext', 'dojo') ) )
				$this->options['library'] = 'base';
			update_option ( 'shadowbox', $this->options );
		}
		*/
		$this->check_upgrade();
	}
	
	/**
	 * Sanitize form values before saving to DB
	 */
	public function before_save_element_values( $values ) {

		extract($values);
		
		$f = $this->admin_settings;
		if ( !array_key_exists( $font_family, $f->families ) && !array_key_exists( $font_family, $f->google_families ) ) { $font_family = ''; }
		if ( !in_array( $font_weight, $f->weights ) ) { $font_weight = ''; }
		if ( !in_array( $font_style, $f->font_styles ) ) { $font_style = ''; }
		if ( !in_array( $text_transform, $f->transforms ) ) { $text_transform = ''; }
		if ( !in_array( $line_height, $f->line_heights ) ) { $line_height = ''; }
		
		$safe = array(
			'active'         => preg_replace( '/[^a-zA-Z0-9]/', '', $active ), // Alphanumeric
			'css'            => strip_tags( $css ),
			'image'          => strip_tags( $image ),
			'bg_color'       => preg_replace( '/[^0-9a-fA-F#]/', '', $color), // Hexadecimal, possibly a-hex (9 chars instead of 7)
			'stops'          => strip_tags( $stops ),
			'color'          => preg_replace( '/[^0-9a-fA-F#]/', '', $color), // Hexadecimal, possibly a-hex (9 chars instead of 7)
			'font_size'      => preg_replace('/[^0-9\.]/', '',$font_size), // Number / decimal
			'font_family'    => $font_family   ,
			'font_weight'    => $font_weight   ,
			'font_style'     => $font_style    ,
			'text_transform' => $text_transform,
			'line_height'    => $line_height   ,
		);
		return $safe;
		
	}
	
	/**
	 * Update/validate the options in the options table from the POST
	 *
	 * @since 0.1
	 * @return none
	 */
	function update( $input ) {
		
		do_action('styles_init', $this->styles);
		do_action('styles_before_process', $this->styles);
		do_action('styles_process', $this->styles);
		do_action('styles_after_process', $this->styles);

		if ( is_array( $input ) ) {
			
			foreach( $input['variables'] as $id => $el ) {
				// Sanatize here before going to DB
				$values = apply_filters( 'styles_before_save_element_values', $el['values'] );
				$this->styles->variables[$id]['values'] = $values;
			}
			
			return $this->styles->variables; // Write to DB
			
		}else {
			return false;
		}
		
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
			
			if ( update_option('styles-preview', $_POST ) ) {
				$response['message'] .= 'Preview variables updated.<br/>';
			}else {
				$response['message'] .= 'Preview variables unchanged.<br/>';
			}
			
		}else {
			
			if ( !update_option('styles', $_POST ) ) {
				$response['message'] .= 'Variables unchanged.<br/>';
			}

			$cache_file = $this->styles->file_paths['cache_path'];
			$cache_nicename = str_replace(ABSPATH, '/', $cache_file);

			if ( $cache_file !== false && @file_put_contents($cache_file, $this->styles->css->contents) ) {
				// Cache written to file
				$response['message'] .= 'Stylesheet rendered and cached to <a href="'.site_url().$cache_nicename.'">'.$cache_nicename.'</a>.<br/>';
			}else {
				$response['message'] = '<div>Could not write to  <code>'.$cache_nicename.'</code> directory.<br/> CSS has been cached to the database instead. This can be changed by making the directory writable with <code>chmod 666</code></div>';
				update_option( 'styles-cache', '/* Styles outputted inline because cache directory "'.$cache_nicename.'" is not writable */'."\r". Minify_CSS_Compressor::process($this->styles->css->contents) );
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
	 * Add the options page
	 *
	 * @return none
	 * @since 0.1
	 */
	function add_page() {
		if ( current_user_can ( 'manage_options' ) ) {
			$this->options_page_hookname = add_theme_page ( __( 'Styles', 'styles' ), __( 'Styles', 'styles' ), 'manage_options', 'styles', array( $this, 'admin_page' ) );
			add_action ( "admin_print_scripts-{$this->options_page_hookname}", array( $this, 'admin_js' ) );
			add_action ( "admin_print_styles-{$this->options_page_hookname}", array( $this, 'admin_css' ) );
			add_filter ( "plugin_action_links_{$this->plugin_basename}", array( $this, 'filter_plugin_actions' ) );

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
		$settings_link = '<a href="themes.php?page=StormStyles">' . __( 'Settings', 'styles' ) . '</a>'; 
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
		if ( $_POST['action'] == 'styles-update-options' && check_admin_referer('styles-update-options') ) {
			// Uses $this->update() sanitation callback
			update_option('styles', $_POST );
		}else {
			do_action('styles_init', $this->styles);
			do_action('styles_before_process', $this->styles);
			do_action('styles_process', $this->styles);
			do_action('styles_after_process', $this->styles);
			do_action('styles_settings', $this->styles);
		}

		$this->load_view('admin.php');
	}

} // END class StormStylesAdminController extends StormStyles

?>