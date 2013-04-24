<?php

class Styles_Customize {

	/**
	 * @var Styles_Plugin
	 */
	var $plugin;

	function __construct( $plugin ) {
		$this->plugin = $plugin;

		add_action( 'customize_register', array( $this, 'add_sections' ), 10 );
		add_action( 'customize_controls_enqueue_scripts',  array( $this, 'enqueue_scripts' ) );

		// Set storm-styles option to not autoload; does nothing if setting already exists
		add_option( Styles_Helpers::get_option_key(), '', '', 'no' );

	}

	public function enqueue_scripts() {

		// Stylesheets
		wp_enqueue_style(  'styles-customize', plugins_url( '/css/styles-customize.css', STYLES_BASENAME ), array(), $this->plugin->version );

		// Javascript
		wp_enqueue_script( 'styles-customize', plugins_url( '/js/styles-customize.js', STYLES_BASENAME ), array(), $this->plugin->version );

	}

	/**
	 * Register sections with WordPress theme customizer in WordPress 3.4+
	 * e.g., General, Header, Footer, Content, Sidebar
	 */
	function add_sections( $wp_customize ) {
		global $wp_customize;

		$i = 950;
		foreach ( $this->plugin->theme->get_settings() as $group => $elements ) {
			$i++;
			
			// Groups
			$group_id = Styles_Helpers::get_group_id( $group );
			$wp_customize->add_section( $group_id, array(
				'title'    => __( $group, 'storm' ),
				'priority' => $i,
			) );

			self::add_items( $group_id, $elements );
		}
	}


	/**
	 * Register individual customize fields in WordPress 3.4+
	 * Settings & Controls are within each class (type calls different classes)
	 */
	public function add_items( $group_id, $elements ) {
		static $i;
		foreach ( $elements as $element ) {
			$i++;
			$element['priority'] = $i;
			if ( $class = Styles_Helpers::get_element_class( $element ) ) {

				// PHP <= 5.2 support
				// Otherwise, would be: $class::add_item( $group_id, $element );
				call_user_func_array( $class.'::add_item', array( $group_id, $element ) );
			}
		}

	}

}