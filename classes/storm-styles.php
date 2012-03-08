<?php

/**
 * Common actions between admin and front-end.
 **/
class Storm_Styles {
	
	/**
	 * Plugin Version
	 *
	 * Holds the current plugin version.
	 *
	 * @var int
	 **/
	var $version = '0.5.1.1';
	
	/**
	 * Plugin DB version
	 * 
	 * Holds the current plugin database version. 
	 * Not the same as the current plugin version.
	 * 
	 * @var int
	 **/
	var $db_version = '0.5.0';
	
	/**
	 * Options array containing all options for this plugin
	 * 
	 * @var string
	 **/
	var $options;
	
	/**
	 * WordPress plugin utilites & setup
	 * 
	 * @var Storm_WP_Admin | Storm_WP_Frontend
	 **/
	var $wp;
	
	/**
	 * Paths to search for a source CSS file
	 * 
	 * @var array
	 **/
	var $search_paths;
	
	/**
	 * CSS source
	 * 
	 * @var string
	 **/
	var $source;
	
	/**
	 * CSS file info: array('uri', 'path', 'cache_path', 'cache_uri')
	 * 
	 * @var array
	 **/
	var $file_paths = false;
	
	/**
	 * Stores values from GUI input, outputs GUI fields and parsed CSS
	 * 
	 * @var array
	 **/
	var $variables    = array( /*
		'general_window' => array(		// element ID - generated from Group/Label or explicitly set
			'form_id'    => '', // input element ID
			'form_name'  => '', // input element name
			'id'         => '', // repeat of 'general_window'
			'label'      => '', // Form label
			'group'      => '', // Form group
			'selector'   => '', // CSS selector
			'values' => array(  // Values set by form inputs
				'active'         => '', // Which elements should be output
				'css'            => '', // CSS value for the active element (needs refactoring to allow multiple)
				'image'          => '', // Image URL
				'image_replace'  => '', // (bool) Whether or not to run the image replace filter
				'bg_color'       => '', // Solid color background
				'stops'          => '', // Gradient background
				'color'           => '', // Font color
				'font_size'      => '',
				'font_family'    => '',
				'font_weight'    => '',
				'font_style'     => '',
				'text_transform' => '',
				'line_height'     => '',
			),
		),
		etc...
	*/ );
	
	/**
	 * Organizes variable keys by group
	 * 
	 * @var array
	 **/
	var $groups = array( /*
		'General' => array(
			'general_window',
			'general_link',
		)
	*/ );
	
	public function __construct() {

		// Load WordPress Utilties
		if ( is_admin() || DOING_AJAX ) {
			$this->wp = new Storm_WP_Admin( $this );
		}else {
			$this->wp = new Storm_WP_Frontend( $this );
		}

		add_action( 'template_redirect', array($this, 'enqueue_css') );
		
		add_action( 'styles_init',   array($this, 'load_variables'),  5, 1 );
		add_action( 'styles_init',   array($this, 'get_file'),       10, 1 );
		add_action( 'styles_init',   array($this, 'parse_css'),      15, 1 );
		add_action( 'styles_render', array($this, 'render'),         10, 1 );
		
	}
	
	function search_paths() {
		if ( !empty( $this->search_paths ) ) {
			return $this->search_paths;
		}
		
		$upload_dir = wp_upload_dir();
		
		if ( !empty($_GET['file']) ) {
			$search_paths = array(
				$_GET['file'],
				trailingslashit(ABSPATH).$_GET['file'],
			);
		}

		$search_paths[] = $upload_dir['basedir'].'/styles/'.get_template().'.gui.css';
		$search_paths[] = get_stylesheet_directory().'/styles.gui.css';
		// $search_paths[] = $this->wp->plugin_dir_path().'themes/'.get_template().'.gui.css';
		
		$this->search_paths = apply_filters('styles_search_paths', $search_paths);
		
		return $this->search_paths;
	}

	/**
	 * Load either live or preview options based on $_GET
	 */
	public function load_variables() {
		
		if ( isset($_GET['preview']) ) {
			$this->variables = get_option( 'styles-preview' );
		}else {
			$this->variables = get_option( 'styles' );
		}
		
	}

	/**
	 * Load path info about first CSS file found in search_paths
	 * Contains: 'uri', 'path', 'cache_path', 'cache_uri'
	 * 
	 * @return array 
	 */
	function get_file() {
		if ( is_array($this->file_paths) ) return $this->file_paths;
		
		global $blog_id;
		
		$upload_dir = wp_upload_dir();
		
		$uri = $path = $cache_path = $cache_uri = false;
		
		// Search for CSS file in order of priority and stop at the first one found
		foreach ($this->search_paths() as $file ) {
			if ( file_exists($file) ) { $path = $file; break; }
		}

		if ( empty($path) ) {
			FB::error('Could not find CSS to load for Styles GUI in '.__FILE__.':'.__LINE__);
			$path = false;
			$uri = false;
		}
		
		// URI for enqueing
		$uri = str_replace( ABSPATH, '', $path );

		// Cache file
		$cache_file = $this->get_cache_file();
		
		// Maybe Create Cache File/Folder
		$cache_dir = dirname($upload_dir['basedir'].$cache_file);
		if ( wp_mkdir_p( $cache_dir ) && is_writable( $cache_dir ) ) {
			$cache_path = $upload_dir['basedir'].$cache_file;
			$cache_uri = $upload_dir['baseurl'].$cache_file;
		}else {
			$cache_path = false;
		}
		
		// Put all info into an array
		$array = compact('uri', 'path', 'cache_path', 'cache_uri');
		
		$this->file_paths = apply_filters( 'styles_file_path', $array );

		return $this->file_paths;
	}
	
	function get_cache_file() {
		
		if ( is_multisite() ) {
			$cache_file = "/styles/cache-$blog_id.css";
		}else {
			$cache_file = "/styles/cache.css";
		}
		return $cache_file;
	}
	
	/**
	 * Extract IDs, labels, groups, etc for the GUI from CSS
	 */
	function parse_css() {
		global $wp_settings_errors;
		
		$contents = @file_get_contents( $styles->file_paths['path'] );
		
		if ( empty($contents) ) {
			$contents = get_option( 'styles-'.get_template() );
		}
		if ( empty($contents) && empty($wp_settings_errors) ) {
			// Just in case the API didn't send this error
			add_settings_error( 'styles-api-key', 'no-css', 'Sorry, '.get_template().' is either not supported or could not be loaded. You can request support for this theme <a href="https://www.google.com/moderator/?authuser=2#16/e=1f6d0a">on this page</a>.', 'error' );			
		}
		
		// Interpret CSS only if:
		if ( !(
			is_admin()                    // We're loading the wp-admin Styles page
			|| isset($_GET['scaffold'])   // Responding to a live redraw via parse_request: site.com/?scaffold
			|| DOING_AJAX                 // Saving the cache via AJAX
		) ){
			return false;
		}

		$this->css = new Storm_CSS_Processor( $this, $contents );
		
	}
	
	/**
	 * Initialize files object based on WordPress style queue
	 **/
	function enqueue_css() {
		global $blog_id;
		
		$upload_dir = wp_upload_dir();
		$cache_file = $this->get_cache_file();
		$cache_path = $upload_dir['basedir'].$cache_file;
		$cache_uri = $upload_dir['baseurl'].$cache_file;
		
		if ( BSM_DEVELOPMENT === true ){
			
			// Development: Force re-render on every CSS load
			wp_enqueue_style('storm-styles', '/?scaffold', array(), time() );
			
		}else if ( file_exists($cache_path) ) {
			
			// Enqueue cached output
			wp_enqueue_style('storm-styles', $cache_uri );

		}else {
			
			// No cache file. Load from DB cache
			add_action( 'wp_head', array($this, wp_head_output), 999 );
			
		}

	}
	
	function wp_head_output() {
		$css = get_option('styles-cache');
		if (!empty($css)) {
			echo "<style id='storm-scaffold-css'>$css</style>";
		}
	}
	
	function render() {
		
		if ( isset( $_GET['scaffold'] ) ) {
			// Output to browser
			header('Content-type: text/css');
			echo $this->css->contents;
			exit;
		}
		
	}
}
