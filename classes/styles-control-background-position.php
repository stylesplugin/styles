<?php

class Styles_Control_Background_Position extends Styles_Control {
	var $suffix = 'background position';
	var $template = '$selector { background-position: $x_value$x_unit $y_value$y_unit; }';
	// [ [ <percentage> | <length> | left | center | right ] [ <percentage> | <length> | top | center | bottom ]? ] | [ [ left | center | right ] || [ top | center | bottom ] ] | inherit

	public function __construct( $group, $element ) {
		$this->default = array(
			'x_unit' => 'px',
			'y_unit' => 'px',
		);
		parent::__construct( $group, $element );
	}

	/**
	 * Register item with $wp_customize
	 */
	public function add_item() {
		global $wp_customize;

		$subsettings = array(
			'x_value',
			'x_unit',
			'y_value',
			'y_unit',
		);
		foreach ( $subsettings as $subsetting ) {
			$wp_customize->add_setting(
				sprintf( '%s[%s]', $this->setting, $subsetting ),
				$this->get_setting_args( $subsetting )
			);
		}

		$control_args = $this->get_control_args();
		$control_args['settings'] = array();
		foreach( $subsettings as $subsetting ) {
			$control_args['settings'][$subsetting] = sprintf( '%s[%s]', $this->setting, $subsetting );
		}
		$control = new Styles_Customize_Background_Position_Control(
			$wp_customize,
			Styles_Helpers::get_control_id( $this->id ),
			$control_args
		);
		$wp_customize->add_control( $control );
	}

	/**
	 * Return CSS based on setting value
	 */
	public function get_css(){
		$selector = $this->selector;
		$value = $this->get_element_setting_value();

		$css = '';
		if ( $value ) {
			$args = array_merge(
				array( 'template' => $this->template, ),
				$value
			);
			$css = $this->apply_template( $args );
		}
		// Filter effects final CSS output, but not postMessage updates
		return apply_filters( 'styles_css_background_position', $css );
	}

	public function post_message( $js ) {
		$js .= str_replace(
			array( '@setting@', '@selector@' ),
			array( $this->setting, $this->jquery_selector() ),
			file_get_contents( STYLES_DIR . '/js/post-message-part-background-position.js' )
		);

		return $js . PHP_EOL;
	}

}


if (class_exists('WP_Customize_Control')) :

class Styles_Customize_Background_Position_Control extends WP_Customize_Control {
	public $units;
	public $type = 'background_position';
	public $dimension_schema;

	function __construct( $manager, $id, $args = array() ) {
		$this->units = array( 'px', '%', 'em', 'ex', 'in', 'cm', 'mm', 'pt', 'pc', );
		$this->dimension_schema = array(
			'x' => array(
				'label' => __( 'X:', 'styles' ),
				'keyword_values' => array(
					'left' => array(
						'percent' => '0',
						'label' => _x( 'Left', 'horizontal dimension', 'styles' ),
					),
					'center' => array(
						'percent' => '50',
						'label' => _x( 'Center', 'horizontal dimension', 'styles' ),
					),
					'right' => array(
						'percent' => '100',
						'label' => _x( 'Right', 'horizontal dimension', 'styles' ),
					),
				),
			),
			'y' => array(
				'label' => __( 'Y:', 'styles' ),
				'keyword_values' => array(
					'top' => array(
						'percent' => '0',
						'label' => _x( 'Top', 'vertical dimension', 'styles' ),
					),
					'center' => array(
						'percent' => '50',
						'label' => _x( 'Center', 'vertical dimension', 'styles' ),
					),
					'bottom' => array(
						'percent' => '100',
						'label' => _x( 'Bottom', 'vertical dimension', 'styles' ),
					),
				),
			),
		);
		parent::__construct( $manager, $id, $args );
	}

	public function render_content() {
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php
		foreach ( array_keys( $this->dimension_schema ) as $dimension ){
			$this->render_dimension_field( $dimension );
		}
	}

	public function render_dimension_field( $dimension ) {
		$dimension_info = $this->dimension_schema[$dimension];
		$saved_unit = $this->value( sprintf( '%s_unit', $dimension ) );
		?>
		<span class="<?php echo esc_attr( "background-position-dimension $dimension" ) ?>">
			<label><?php echo esc_attr( $dimension_info['label'] ) ?>
				<input
					type="number"
					value="<?php echo esc_attr( $this->value( sprintf( '%s_value', $dimension ) ) ); ?>"
					<?php $this->link( sprintf( '%s_value', $dimension ) ); ?>
					class="styles-background-position-value"
				/><select
					data-unit-setting="<?php echo esc_attr( $this->settings[ sprintf( '%s_unit', $dimension ) ]->id ) ?>"
					data-value-setting="<?php echo esc_attr( $this->settings[ sprintf( '%s_value', $dimension ) ]->id ) ?>"
					data-dimension="<?php echo esc_attr( $dimension ) ?>"
					class="styles-background-position-unit-keywords"
					>
					<?php foreach( $dimension_info['keyword_values'] as $keyword => $info ): ?>
						<option data-percent="<?php echo esc_attr( $info['percent'] ) ?>" data-keyword="<?php echo esc_attr( $keyword ) ?>">
							<?php echo esc_html( $info['label'] ) ?>
						</option>
					<?php endforeach; ?>
					<?php foreach( $this->units as $unit ): ?>
						<option value="<?php echo esc_attr( $unit ) ?>">
							<?php echo esc_html( $unit ) ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>
		</span>
		<?php
	}
}

endif;