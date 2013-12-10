<?php

class Styles_Admin {

	/**
	 * @var Styles_Plugin
	 */
	var $plugin;

	/**
	 * @var array All sections
	 */
	var $sections;

	/**
	 * @var array All settings
	 */
	var $settings;

	/**
	 * Admin notices
	 */
	var $notices = array();

	/**
	 * List of theme slugs we know have styles plugins on wordpress.org
	 */
	var $default_themes = array(
		'twentyten',
		'twentyeleven',
		'twentytwelve',
		'twentythirteen',
	);

	/**
	 * @var Styles_License
	 */
	var $license;

	function __construct( $plugin ) {
		$this->plugin = $plugin;

		// Settings
		$this->sections_init(); 
		$this->settings_init(); 

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		// Notices
		add_action( 'admin_init', array( $this, 'install_default_themes_notice' ), 20 );
		add_action( 'admin_init', array( $this, 'activate_notice' ), 30 );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_notices' ), 11 );

		// Scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Plugin Meta
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		// License Menu
		add_action( 'admin_menu', array( $this, 'license_menu' ) );
	}

	/**
	 * Enqueue admin stylesheet
	 * Adds the blue "Customize" button to the plugin row.
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'storm-styles-admin', plugins_url('css/styles-admin.css', STYLES_BASENAME), array(), $this->plugin->version, 'all' );
	}

	/**
	 * Populate $this->sections with all section arguements
	 * 
	 * @return void
	 */
	public function sections_init() {
		$this->sections = array(
			'debug' => __( 'Debug', 'styles' ),
			'reset' => __( 'Reset', 'styles' ),
		);
	}

	/**
	 * Populate $this->settings with all settings arguements
	 * 
	 * @return void
	 */
	public function settings_init() {
		$this->settings = array(

			'debug_mode' => array(
				'title'       => __( 'Debug Mode', 'styles' ),
				'description' => __( 'Allow information about your settings and WordPress setup to be viewed by Styles support.', 'styles' ),
				'default'     => 0,
				'type'        => 'radio',
				'section'     => 'debug',
				'class'       => '',
				'choices'     => array(
					'Enable' => 1,
					'Disable' => 0,
				),
			),

			'delete_settings' => array(
				'title'       => __( 'Reset Settings', 'styles' ),
				'description' => __( 'Type DELETE into this field and save to verify a reset of all Styles settings.', 'styles' ),
				'default'     => '',
				'type'        => 'input',
				'section'     => 'reset',
			),

		);
	}

	public function admin_menu() {
		
		add_options_page(
			'Styles',                       // Page title
			'Styles',                       // Menu title
			'manage_options',               // Capability
			'styles',                       // Menu slug
			array( $this, 'admin_options' ) // Page display callback
		);

	}

	/**
	 * Output the options page view.
	 * 
	 * @return null Outputs views/licenses.php and exits.
	 */
	function admin_options() {
		$this->plugin->get_view( 'admin-options' );
	}

	/**
	* Register settings
	*/
	public function register_settings() {
		
		register_setting( 'styles', 'storm-styles', array ( $this, 'validate_settings' ) );
		
		foreach ( $this->sections as $slug => $title ) {
			add_settings_section(
				$slug,
				$title,
				null, // Section display callback
				'styles'
			);
		}
		
		foreach ( $this->settings as $id => $setting ) {
			$setting['id'] = $id;
			$this->create_setting( $setting );
		}
		
	}

	/**
	 * Create settings field
	 *
	 * @since 1.0
	 */
	public function create_setting( $args = array() ) {
		
		$defaults = array(
			'id'          => 'default_field',
			'title'       => __( 'Default Field', 'styles' ),
			'description' => __( 'Default description.', 'styles' ),
			'default'     => '',
			'type'        => 'text',
			'section'     => 'general',
			'choices'     => array(),
			'class'       => ''
		);
			
		extract( wp_parse_args( $args, $defaults ) );
		
		$field_args = array(
			'type'        => $type,
			'id'          => $id,
			'description' => $description,
			'default'     => $default,
			'choices'     => $choices,
			'label_for'   => $id,
			'class'       => $class
		);
		
		add_settings_field(
			$id,
			$title,
			array( $this, 'display_setting' ),
			'styles',
			$section,
			$field_args
		);
	}

	/**
	 * Load view for setting, passing arguments
	 */
	public function display_setting( $args = array() ) {
		
		$id = $args['id'];
		$type = $args['type'];

		$options = get_option( 'storm-styles' );
		
		if ( !isset( $options[$id] ) ) {
			$options[$id] = $default;
		}
		
		$args['option_value'] = $options[ $id ];
		$args['option_name'] = 'storm-styles' . '[' . $id . ']';

		$template = 'setting-' . $type;

		$this->plugin->get_view( $template, $args );
		
	}

	/**
	* Validate settings
	*/
	public function validate_settings( $input ) {

		// Combine input with general settings
		$input = array_merge( get_option('storm-styles'), $input );

		// Todo: Sanatize.
		return $input;

	}

	/**
	 * Add additional links to the plugin row
	 * For example, "Customize"
	 *
	 * Change the title of "Visit plugin site"
	 */
	public function plugin_row_meta( $meta, $basename ) {
		if ( STYLES_BASENAME == $basename ) {
			$meta[2] = str_replace( 'Visit plugin site', 'Get More Themes', $meta[2] );
			$meta[] = '<a class="button button-primary" href="' . admin_url( 'customize.php' ) . '">Customize Theme</a>';
		}
		return $meta;
	}

	/**
	 * Notice for novice users.
	 *
	 * If a default theme is active, but no Styles add-on is active,
	 * display a prompt with a link to install the add-on from wordpress.org
	 *
	 * Does not run if:
	 *   Active template is not in $this->default_themes
	 *   Any active or inactive plugin declares support for the current theme
	 *   `styles_disable_notices` filter returns true
	 *      Example: <code>add_filter( 'styles_disable_notices', '__return_true' );</code>
	 */
	public function install_default_themes_notice() {
		if (
			apply_filters( 'styles_disable_notices', false )
			|| $this->is_plugin_update_or_delete()
			|| !in_array( get_stylesheet(), $this->default_themes ) // Active theme is a parent and default
		) {
			return false;
		}

		$plugin_installed = false;
		if ( is_a( $this->plugin->child, 'Styles_Child' ) ) {

			$all_styles_plugins = array_merge( (array) $this->plugin->child->plugins, (array) $this->plugin->child->inactive_plugins );
			
			foreach ( $all_styles_plugins as $plugin ) {
				if ( $plugin->is_target_theme_active() ) {
					// This plugin is for the active theme, but is inactive
					$plugin_installed = true;
				}
			}
		}

		if ( !$plugin_installed ) {
			$theme = wp_get_theme();
			$slug = 'styles-' . get_template();
			$url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $slug ), 'install-plugin_' . $slug );
			$this->notices[] = "<p>Styles is almost ready! To add theme options for <strong>{$theme->name}</strong>, please <a href='$url'>install Styles: {$theme->name}</a>.</p>";
			return true;
		}
	}

	/**
	 * Notice for novice users.
	 * 
	 * If an inactive plugin declares support for the currently active theme,
	 * display a notice with a link to active the plugin.
	 * 
	 * Does not run if:
	 *   `styles_disable_notices` filter returns true
	 *      Example: <code>add_filter( 'styles_disable_notices', '__return_true' );</code>
	 */
	public function activate_notice() {
		if (
			apply_filters( 'styles_disable_notices', false )
			|| $this->is_plugin_update_or_delete()
			|| !is_a( $this->plugin->child, 'Styles_Child' ) // No child plugins installed
		) {
			return false;
		}

		foreach ( (array) $this->plugin->child->inactive_plugins as $plugin ) {
			if ( $plugin->is_target_theme_active() ) {
				// This plugin is for the active theme, but is inactive
				$theme_name = $plugin->theme->get('Name');
				$url = wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin=' . $plugin->plugin_basename ), 'activate-plugin_' . $plugin->plugin_basename );
				$this->notices[] = "<p><strong>{$plugin->name}</strong> is installed, but not active. To add Styles support for $theme_name, please <a href='$url'>activate {$plugin->name}</a>.</p>";
			}
		}

	}

	/**
	 * Check whether we're on a screen for updating or deleting plugins.
	 * If we are, return false to disable notices.
	 */
	public function is_plugin_update_or_delete() {
		if ( 'update.php' == basename( $_SERVER['PHP_SELF'] )
			|| ( isset( $_GET['action'] ) && 'delete-selected' == $_GET['action'] )
			|| !current_user_can('install_plugins')
		){
			return true;
		}else {
			return false;
		}
	}

	/**
	 * Output all notices that have been added to the $this->notices array
	 */
	public function admin_notices() {
		foreach( $this->notices as $key => $message ) {
			echo "<div class='updated fade' id='styles-$key'>$message</div>";
		}
	}

	/**
	 * Allows notices to display in the customize.php sidebar
	 * 
	 * @return null Passes $this->notices array to styles-customize-controls.js
	 */
	public function customize_notices() {
		wp_localize_script( 'styles-customize-controls', 'wp_styles_notices', $this->notices );
	}

	/**
	 * Add the Styles Licenses page if any plugins require license keys for updating.
	 * 
	 * @return null
	 */
	function license_menu() {
		$plugins = apply_filters( 'styles_license_form_plugins', array() );

		if ( !empty( $plugins ) ) {
			add_plugins_page( 'Styles Licenses', 'Styles Licenses', 'manage_options', 'styles-license', array( $this, 'license_page' ) );
		}
	}

	/**
	 * Output the Styles License page view.
	 * 
	 * @return null Outputs views/licenses.php and exits.
	 */
  function license_page() {
      require_once STYLES_DIR . '/views/licenses.php';
      exit;
  }

}