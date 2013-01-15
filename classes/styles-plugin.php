<?php

/**
 * Common actions between admin and front-end.
 **/
class Styles_Plugin {
	
	/**
	 * Plugin Version
	 *
	 * Holds the current plugin version.
	 *
	 * @var int
	 **/
	var $version = '0.5.3';
	
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

		add_action( 'customize_register', 'Styles_Plugin::customize_register', 10 );
		add_action( 'wp_head', 'Styles_Plugin::wp_head', 999 );
		
	}

	/**
	 * Add settings to WP Customizer
	 */
	public static function customize_register( $wp_customize ) {
		require_once dirname( __FILE__ ) . '/styles-helpers.php';
		require_once dirname( __FILE__ ) . '/styles-customizer.php';

		Styles_Customizer::add_sections( $wp_customize );
	}

	/**
	 * Output CSS
	 */
	public static function wp_head() {
		require_once dirname( __FILE__ ) . '/styles-helpers.php';
		require_once dirname( __FILE__ ) . '/styles-css.php';
		Styles_CSS::output_css();
	}

}





/*
switch ( $type ) {
	case 'open-section':
		$wp_customize->add_setting( "styles[$group][$id][values][subsection]", array(
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
	case 'gradient':
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
*/