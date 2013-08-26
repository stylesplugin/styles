<?php

class Styles_Customize {

	/**
	 * @var Styles_Plugin
	 */
	var $plugin;

	/**
	 * Customize settings loaded from customize.json in plugin or theme
	 * @var array
	 */
	var $settings = array();

	/**
	 * Styles_Control objects that register with wp_customize
	 *
	 * @var array contains Styles_Control objects
	 */
	var $controls;

	function __construct( $plugin ) {
		$this->plugin = &$plugin;

		add_action( 'customize_register', array( $this, 'add_sections' ), 10 );
		add_action( 'customize_controls_enqueue_scripts',  array( $this, 'customize_controls_enqueue' ) );
		add_action( 'customize_preview_init',  array( $this, 'customize_preview_init' ) );

		// Load settings from various sources with filters
		add_filter( 'styles_json_files', array( $this, 'load_settings_from_child_plugin' ), 50 );
		add_filter( 'styles_json_files', array( $this, 'load_settings_from_theme' ), 70 );

		// Set storm-styles option to not autoload; does nothing if setting already exists
		add_option( Styles_Helpers::get_option_key(), '', '', 'no' );

	}

	public function customize_preview_init() {
		add_action( 'wp_footer', array( $this, 'preview_js' ) );
	}

	public function customize_controls_enqueue() {

		// Stylesheets
		wp_enqueue_style(  'styles-customize', plugins_url( '/css/styles-customize.css', STYLES_BASENAME ), array(), $this->plugin->version );

		// Javascript
		wp_enqueue_script( 'styles-customize-controls', plugins_url( '/js/styles-customize-controls.js', STYLES_BASENAME ), array(), $this->plugin->version );

	}

	/**
	 * Output javascript for WP Customizer preview postMessage transport
	 */
	public function preview_js() {
		// Ensure dependencies have been output by now.
		wp_print_scripts( array( 'jquery', 'customize-preview' ) );

		?>
		<script>
			( function( $ ){

				<?php echo apply_filters( 'styles_customize_preview', '' ) ?>

			} )( jQuery );
		</script>
		<?php
	}

	/**
	 * Register sections with WordPress theme customizer in WordPress 3.4+
	 * e.g., General, Header, Footer, Content, Sidebar
	 */
	function add_sections( $wp_customize ) {
		global $wp_customize;

		$i = 950;
		foreach ( (array) $this->get_settings() as $group => $elements ) {
			$i++;
			
			// Groups
			$group_id = Styles_Helpers::get_group_id( $group );
			$has_section = (bool) $wp_customize->get_section( $group_id );
			if ( ! $has_section ) {
				$wp_customize->add_section( $group_id, array(
					'title'    => __( $group, 'storm' ),
					'priority' => $i,
				) );
			}

			$this->add_items( $group_id, $elements );
		}
	}


	/**
	 * Register individual customize fields in WordPress 3.4+
	 * Settings & Controls are within each class (type calls different classes)
	 */
	public function add_items( $group_id, $elements, $add_item = true ) {
		static $i;
		foreach ( (array) $elements as $element ) {
			$i++;
			$element['priority'] = $i;
			if ( $class = Styles_Helpers::get_element_class( $element ) ) {

				// PHP <= 5.2 support
				// Otherwise, would be: $class::add_item( $group_id, $element );
				$control = new $class( $group_id, $element );
				
				if ( $add_item ) {
					$control->add_item();
				}

				$this->controls[] = $control;
			}
		}

	}

	/**
	 * Load settings as JSON either from transient / API or theme file
	 *
	 * @return array
	 */
	public function get_settings() {

		// Return cached settings if they've already been processed
		if ( !empty( $this->settings ) ) {
			return $this->settings;
		}

		// Plugin Authors: Filter to provide arbitrary JSON file paths
		foreach( (array) apply_filters( 'styles_json_files', array() ) as $json_file ) {
			$this->settings = $this->load_settings_from_json_file( $json_file, $this->settings );
		}

		// Last-second filter to edit settings as PHP array instead of JSON
		return apply_filters( 'styles_settings', $this->settings );
	}

	/**
	 * Load settings from plugin that inherits a Styles class,
	 * like Styles_Child_Theme, which expects customize.json to be in plugin directory
	 */
	public function load_settings_from_child_plugin( $json_files ) {

		// Plugins that declare styles class: XXX in header
		foreach( (array) $this->plugin->child->plugins as $plugin ) {
			// Class contains method get_json_path()
			if( method_exists( $plugin, 'get_json_path' ) ) {
				$json_files[] = $plugin->get_json_path();
			}
		}

		return $json_files;
	}

	/**
	 * Load settings from theme file formatted as JSON
	 */
	public function load_settings_from_theme( $json_files ) {
		$json_files[] = get_stylesheet_directory() . '/customize.json';
		return $json_files;
	}

	public function load_settings_from_json_file( $json_file, $default_settings = array() ) {
		$settings = array();
		if ( file_exists( $json_file ) ) {
			$json =  preg_replace('!/\*.*?\*/!s', '', file_get_contents( $json_file ) ); // strip comments before decoding
			$settings = json_decode( $json, true );

			if ( $json_error = Styles_Helpers::get_json_error( $json_file, $settings ) ) {
				wp_die( $json_error );
			}
		}
		return wp_parse_args( $settings, $default_settings );
	}

}