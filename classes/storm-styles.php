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
	var $version = '0.5.2';
	
	/**
	 * Plugin DB version
	 * 
	 * Holds the current plugin database version. 
	 * Not the same as the current plugin version.
	 * 
	 * @var int
	 **/
	var $db_version = '0.5.0';
	
	public function __construct() {

		// Setup Customizer
		add_action( 'customize_register', 'Storm_Styles_Customizer::sections', 10 );
		// add_action( 'customize_register', 'Storm_Styles_Customizer::items', 11 );


		
		// Render CSS
		// Render preview CSS
		
	}


}


class Storm_Styles_Customizer {
	/**
	 * Register sections with WordPress theme customizer in WordPress 3.4+
	 * e.g., General, Header, Footer, Content, Sidebar
	 */
	static function sections( $wp_customize ) {
		// Maybe move to storm-wp-admin.php
		// do_action( 'styles_init', $this->styles );
		// do_action( 'styles_before_process', $this->styles );
		// do_action( 'styles_process', $this->styles );
		// do_action( 'styles_after_process', $this->styles );

		$wp_customize->add_section( 'test', array(
			'title'    => __( 'Test', 'themename' ),
			'priority' => 1,
		) );


		//// ------ test
		$js_id = 'test';
		$group = 'test';
		$label = 'test';
		$id = 'test';

		$suffix = ' Background Color';
		$wp_customize->add_setting( "styles[$id][values][css]", array(
			'default'    => '',
			'type'       => 'option',
			'capability' => 'edit_theme_options',
			// 'transport'      => 'postMessage',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "storm_$js_id", array(
			'label'    => __( $label.$suffix, 'styles' ),
			'section'  => "$group",
			'settings' => "styles[$id][values][css]",
			'priority' => $priority.'1',
		) ) );
		//// ------ test

		// GUI
		// $i = 950;
		// foreach ( $this->styles->groups as $group => $elements ) {
		// 	$wp_customize->add_section( $group, array( // Namespace as storm_$group in future
		// 		'title'    => __( $group, 'storm' ),
		// 		'priority' => $i,
		// 	) );
		// 	$i++;
		// }
	}


	/**
	 * Register individual customize fields in WordPress 3.4+
	 */
	public function items( $wp_customize ) {
		//FB::log( __FUNCTION__ );

		//FB::log( $this->styles->groups, '$this->styles->groups' );
		//FB::log($this->styles->variables, '$this->styles->variables');

		// GUI
		foreach ( $this->styles->variables as $key => $element ) {
			if ( empty( $element['selector'] ) ) {
				// Skip items that don't exist in the current theme
				continue;
			}

			if ( !in_array( $element['id'], $this->styles->groups[$element['group']] ) ) {
				continue;
			}

			// $form_id, $form_name, $id, $label, $group,$selector
			// $values[ active,css,image,bg_color,stops,$color,
			// 	$font_size, $font_family, $font_weight,
			// 	$font_style, $text_transform, $line_height ]
			extract( $element );
			list( $x, $type) = explode( '_', $id );
			$js_id = str_replace( '.', '_', $id );

			switch ( $type ) {
				case 'open-section':
					$wp_customize->add_setting( "styles[$id][values][subsection]", array(
						'default'    => '',
						'type'       => 'option',
						'capability' => 'edit_theme_options',
						// 'transport'      => 'postMessage',
					) );

					$wp_customize->add_control( new Styles_Customize_Subsection_Control( $wp_customize, "storm_$js_id", array(
						'label'    => "$label",
						'section'  => "$group",
						'settings' => "styles[$id][values][subsection]",
						'priority' => $priority.'0',
					) ) );
					break;
				case 'background-color':
					$suffix = ' Background Color';
					$wp_customize->add_setting( "styles[$id][values][css]", array(
						'default'    => '',
						'type'       => 'option',
						'capability' => 'edit_theme_options',
						// 'transport'      => 'postMessage',
					) );
					$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "storm_$js_id", array(
						'label'    => __( $label.$suffix, 'styles' ),
						'section'  => "$group",
						'settings' => "styles[$id][values][css]",
						'priority' => $priority.'1',
					) ) );
					break;
				/*case 'gradient':
					$suffix = ' Background Gradient';
					$wp_customize->add_setting( "styles[$id][values][css]", array(
						'default'    => '',
						'type'       => 'option',
						'capability' => 'edit_theme_options',
						// 'transport'      => 'postMessage',
					) );
					$wp_customize->add_control( new Styles_Customize_Gradient_Control( $wp_customize, "storm_$js_id", array(
						'label'    => __( $label.$suffix, 'styles' ),
						'section'  => "$group",
						'settings' => "styles[$id][values][css]",
						'priority' => $priority.'1',
					) ) );
					break;*/
				case 'color':
					$suffix = ' Text Color';
					$wp_customize->add_setting( "styles[$id][values][css]", array(
						'default'    => '',
						'type'       => 'option',
						'capability' => 'edit_theme_options',
						// 'transport'      => 'postMessage',
					) );
					$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "storm_$js_id", array(
						'label'    => __( $label.$suffix, 'styles' ),
						'section'  => "$group",
						'settings' => "styles[$id][values][css]",
						'priority' => $priority.'2',
					) ) );
					break;
				case 'font-family':
					$suffix = ' Font Family';
					$wp_customize->add_setting( "styles[$id][values][font_family]", array(
						'default'    => '',
						'type'       => 'option',
						'capability' => 'edit_theme_options',
						// 'transport'      => 'postMessage',
					) );
					$wp_customize->add_control( new Styles_Customize_Font_Family_Control( $wp_customize, "storm_$js_id", array(
						'label'    => __( $label.$suffix, 'styles' ),
						'section'  => "$group",
						'settings' => "styles[$id][values][font_family]",
						'priority' => $priority.'3',
					) ) );
					break;
				case 'font-size':
					$suffix = ' Font Size';
					$wp_customize->add_setting( "styles[$id][values][font_size]", array(
						'default'    => '',
						'type'       => 'option',
						'capability' => 'edit_theme_options',
						// 'transport'      => 'postMessage',
					) );
					$wp_customize->add_control( new Styles_Customize_Text_Pixels_Control( $wp_customize, "storm_$js_id", array(
						'label'    => __( $label.$suffix, 'styles' ),
						'section'  => "$group",
						'settings' => "styles[$id][values][font_size]",
						'priority' => $priority.'4',
					) ) );
					break;
				case 'line-height':
					$suffix = ' Line Height';
					$wp_customize->add_setting( "styles[$id][values][line_height]", array(
						'default'    => '',
						'type'       => 'option',
						'capability' => 'edit_theme_options',
						// 'transport'      => 'postMessage',
					) );
					$wp_customize->add_control( new Styles_Customize_Text_Pixels_Control( $wp_customize, "storm_$js_id", array(
						'label'    => __( $label.$suffix, 'styles' ),
						'section'  => "$group",
						'settings' => "styles[$id][values][line_height]",
						'priority' => $priority.'5',
						'type'     => 'text'
					) ) );
					break;
				case 'font-weight':
					$suffix = ' Font Weight';
					$wp_customize->add_setting( "styles[$id][values][font_weight]", array(
						'default'    => '',
						'type'       => 'option',
						'capability' => 'edit_theme_options',
						// 'transport'      => 'postMessage',
					) );
					$wp_customize->add_control( "storm_$js_id", array(
						'label'    => __( $label.$suffix, 'styles' ),
						'section'  => "$group",
						'settings' => "styles[$id][values][font_weight]",
						'priority' => $priority.'6',
						'type'     => 'select',
						'choices'  => array(
							'' => 'Default',
							'100' => '100',
							'200' => '200',
							'300' => '300',
							'400' => '400 (Normal)',
							'500' => '500',
							'600' => '600',
							'700' => '700 (Bold)',
							'800' => '800',
							'900' => '900',
						),
					) );
					break;
				case 'font-style':
					$suffix = ' Font Style';
					$wp_customize->add_setting( "styles[$id][values][font_style]", array(
						'default'    => '',
						'type'       => 'option',
						'capability' => 'edit_theme_options',
						// 'transport'      => 'postMessage',
					) );
					$wp_customize->add_control( "storm_$js_id", array(
						'label'    => __( $label.$suffix, 'styles' ),
						'section'  => "$group",
						'settings' => "styles[$id][values][font_style]",
						'priority' => $priority.'7',
						'type'     => 'select',
						'choices'  => array(
							'normal' => 'Normal',
							'italic' => 'Italic',
							'oblique' => 'Oblique',
						),
					) );
					break;
				case 'text-transform':
					$suffix = ' Text Transform';
					$wp_customize->add_setting( "styles[$id][values][text_transform]", array(
						'default'    => '',
						'type'       => 'option',
						'capability' => 'edit_theme_options',
						// 'transport'      => 'postMessage',
					) );
					$wp_customize->add_control( "storm_$js_id", array(
						'label'    => __( $label.$suffix, 'styles' ),
						'section'  => "$group",
						'settings' => "styles[$id][values][text_transform]",
						'priority' => $priority.'8',
						'type'     => 'select',
						'choices'  => array(
							'none'  => 'None',
							'capitalize'  => 'Capitalize',
							'uppercase' => 'Uppercase',
							'lowercase' => 'Lowercase'
						),
					) );
					break;
				case 'close-section':
					$wp_customize->add_setting( "styles[$id][values][]", array(
						'default'    => '',
						'type'       => 'option',
						'capability' => 'edit_theme_options',
						// 'transport'      => 'postMessage',
					) );

					$wp_customize->add_control( new Styles_Customize_EndSubsection_Control( $wp_customize, "storm_$js_id", array(
						'label'    => __( 'End', 'styles' ),
						'section'  => "$group",
						'settings' => "styles[$id][values][]",
						'priority' => $priority.'9',
						'type'     => 'endsubsection',
					) ) );
					break;
			}

		}
	}
}