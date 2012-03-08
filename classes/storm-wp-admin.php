<?php
/**
 * All methods and actions required for Styles to work in the admin of WordPress
 **/
class Storm_WP_Admin extends Storm_WP_Frontend {
	
	/**
	 * Name of options page hook
	 *
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
	 * Transient of API returned values
	 *
	 * @var array
	 **/
	var $api_options;

	/**
	 * Container of get_option('styles-settings')
	 *
	 * @var array
	 **/
	var $options;
	
	/**
	 * Setup backend functionality in WordPress
	 *
	 * @return none
	 */
	function __construct( $styles ) {
		parent::__construct( $styles ); // sets $this->styles = $styles

		$defaults = array(
			'file'	=> apply_filters( 'pdstyles_default_file', $this->plugin_dir_path() . 'example/vars.scss' ),
		);
		$args = wp_parse_args( $args, $defaults );
		
		$this->options = get_option( 'styles-settings' );

		if ( version_compare ( $this->get_option ('version'), $this->styles->db_version, '!=' ) ) {
			$this->check_upgrade();
		}
		
		// Load localizations if available
		// load_plugin_textdomain ( 'styles', false, 'styles/localization' );
        
		// Activation hook
		register_activation_hook( $this->plugin_file, array( $this, 'init' ) );
        
		// Whitelist options
		add_action( 'admin_init', array( $this, 'register_settings' ) );
        
		// Activate the options page
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		
		// Rebuild CSS on theme switch
		add_action( 'switch_theme', array($this, 'force_recache') );
        
		// AJAX
		add_action('wp_ajax_styles-update-options', array( $this, 'update_ajax') );
		
		// Media Popup Styles
		add_action('admin_print_styles-media-upload-popup', array($this, 'admin_css_media_upload'));
		add_action('admin_print_scripts-media-upload-popup', array($this, 'admin_js_media_upload'));
		
		// Settings page setup
		$this->admin_settings = new Storm_WP_Settings( $this->styles );
	}
	
	/**
	 * Register settings sanitization methods
	 *
	 * @return none
	 */
	function register_settings() {
		register_setting ( 'styles', 'styles',          array( $this, 'update' ) ); // update = validation method
		register_setting ( 'styles', 'styles-preview',  array( $this, 'update' ) );
	}
	
	/**
	 * Enqueue javascript required for the admin settings page
	 * 
	 * @return none
	 */
	function admin_js() {
		
		wp_enqueue_script('storm-admin-main');
		
		wp_localize_script ( 'storm-admin-main', 'storm_admin', array(
			'mediaUploadURL'	 => admin_url('media-upload.php'),
			'pluginURL'	 => $this->plugin_url(),
		) );
		
	}
	
	/**
	 * Enqueue CSS required for the admin settings page
	 *
	 * @return none
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
	 * Return the default settings
	 *
	 * @author Matt Martz <matt@sivel.net>
	 * @return array
	 */
	function defaults() {
		$defaults = array(
			'version'           => $this->styles->db_version,
			'language'          => $this->set_lang(),
		);
		return $defaults;
	}
	
	/**
	 * Initialize the default options during plugin activation
	 *
	 * @author Matt Martz <matt@sivel.net>
	 * @return none
	 */
	function init() {
		if ( ! get_option ( 'styles-settings' ) ) {
			$this->options = $this->defaults();
			add_option( 'styles-settings', $this->options );
		} else {
			$this->check_upgrade();
		}
	}
	
	/**
	 * Check if an upgrade is needed
	 * 
	 * @return none
	 */
	function check_upgrade() {

		if ( $this->version_compare ( array( '0.5.0' => '<' ) ) ) {

			// Upgrades for versions below 0.5.0
			include dirname(__FILE__)."/upgrade/0.5.0.php"; 
			
			// Check for additional upgrade
			$this->check_upgrade();
			
		} //else if ( $this->version_compare ( array( '0.5.0' => '>', 'X.Y.Z' => '<' ) ) ) {}
			
	}
	
	/**
	 * Compare Versions
	 *
	 * @author Matt Martz <matt@sivel.net>
	 * @param array Array of the version you want to compare to the version stored in the database as the key and the operator as the value
	 * @return bool
	 */
	function version_compare ( $versions ) {
		foreach ( $versions as $version => $operator ) {
			if ( version_compare ( $this->get_option('version'), $version, $operator ) )
				$response = true;
			else
				$response = false; 
		}
		return $response;
	}
	
	/**
	 * Update/validate the options in the options table from the POST
	 *
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
				unset( $this->styles->variables[$id]['selector'] );
			}
			
			return $this->styles->variables; // Write to DB
			
		}else {
			return false;
		}
		
	}
	
	/**
	 * Rebuild and recache CSS. e.g., on theme switch
	 */
	function force_recache() {
		do_action('styles_init', $this->styles);
		do_action('styles_before_process', $this->styles);
		do_action('styles_process', $this->styles);
		do_action('styles_after_process', $this->styles);
		
		$this->write_to_cache();
	}
	
	/**
	 * Write... to the cache.
	 */
	function write_to_cache() {
		$cache_file = $this->styles->file_paths['cache_path'];
		$cache_nicename = str_replace(ABSPATH, '/', $cache_file);

		if ( $cache_file !== false && @file_put_contents($cache_file, $this->styles->css->contents) ) {
			// Cache written to file
			return 'Stylesheet rendered and cached to <a href="'.site_url().$cache_nicename.'">'.$cache_nicename.'</a>.<br/>';
		}else {
			update_option( 'styles-cache', '/* Styles outputted inline because cache directory "'.$cache_nicename.'" is not writable */'."\r". Minify_CSS_Compressor::process($this->styles->css->contents) );
			return '<div>Could not write to  <code>'.$cache_nicename.'</code> directory.<br/> CSS has been cached to the database instead. This can be changed by making the directory writable with <code>chmod 666</code></div>';
		}
	}
	
	/**
	 * Handle updating options via AJAX; cache Scaffold output
	 * 
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

			$response['message'] .= $this->write_to_cache();

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
	 */
	function filter_plugin_actions ( $links ) { 
		$settings_link = '<a href="themes.php?page=styles">' . __( 'Settings', 'styles' ) . '</a>'; 
		array_unshift ( $links, $settings_link ); 
		return $links;
	}
	
	/**
	 * Output the options page
	 *
	 * @return none
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

	/**
	 * Create a potbox widget
	 *
	 * From WordPress SEO by Joost de Valk. Some of the best code in the WP community.
	 *
	 * @author Joost de Valk http://yoast.com/
	 * @link http://yoast.com/wordpress/seo/
	 */
	function postbox($id, $title, $content) {
	?>
		<div id="<?php echo $id; ?>" class="postbox">
			<div class="handlediv" title="Click to toggle"><br /></div>
			<h3 class="hndle"><span><?php echo $title; ?></span></h3>
			<div class="inside">
				<?php echo $content; ?>
			</div>
		</div>
	<?php
	}
	
	/**
	 * Return a list of languages available
	 *
	 * @author Matt Martz <matt@sivel.net>
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
			'zh-TW'  // Chinese (Traditional)
		);
		return $languages;
	}
	
	/**
	 * Try to set pdstyles language based on defined language for WordPress
	 *
	 * @author Matt Martz <matt@sivel.net>
	 * @return string
	 */
	function set_lang() {
		if ( defined ( 'WPLANG' ) )
			$wp_lang = WPLANG;
		else
			$wp_lang = 'en';
	
		switch ( $wp_lang ) {
			case 'ar':    $lang = 'ar';    break; // Arabic
			case 'ca':    $lang = 'ca';    break; // Catalan
			case 'cs_CZ': $lang = 'cs';    break; // Czech
			case 'da_DK': $lang = 'da';    break; // Danish
			case 'de_DE': $lang = 'de-DE'; break; // German
			case 'es_ES': $lang = 'es';    break; // Spanish
			case 'et':    $lang = 'et';    break; // Estonian
			case 'fi':
			case 'fi_FI': $lang = 'fi';    break; // Finnish
			case 'fr_BE': 
			case 'fr_FR': $lang = 'fr';    break; // French
			case 'gl_ES': $lang = 'gl';    break; // Galician
			case 'he_IL': $lang = 'he';    break; // Hebrew
			case 'hu_HU': $lang = 'hu';    break; // Hungarian
			case 'id_ID': $lang = 'id';    break; // Indonesian
			case 'is_IS': $lang = 'is';    break; // Icelandic
			case 'it_IT': $lang = 'it';    break; // Italian
			case 'ja':    $lang = 'ja';    break; // Japanese
			case 'ko_KR': $lang = 'ko';    break; // Korean
			case 'my_MM': $lang = 'my';    break; // Burmese
			case 'nl':
			case 'nl_NL': $lang = 'nl';    break; // Dutch
			case 'nn_NO': $lang = 'no';    break; // Norwegian
			case 'pl_PL': $lang = 'pl';    break; // Polish
			case 'pt_BR': $lang = 'pt-BR'; break; // Brazilian Portuguese
			case 'pt_PT': $lang = 'pt-PT'; break; // Portuguese
			case 'ro':    $lang = 'ro';    break; // Romanian
			case 'ru_RU':
			case 'ru_UA': $lang = 'ru';    break; // Rusian
			case 'sk':    $lang = 'sk';    break; // Slovak
			case 'sv_SE': $lang = 'sv';    break; // Swedish
			case 'tr':    $lang = 'tr';    break; // Turkish
			case 'zh_CN': $lang = 'zh-CN'; break; // Chinese (Simplified)
			default :     $lang = 'en';    break; // English
 
		}
		return $lang;
	}

}