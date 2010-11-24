<?php

/**
 * Attaches a color picker to variables with a hex color format
 * 
 * @since 0.1
 * @package pd-styles
 * @author pdclark
 **/
class PDStyles_Extension_Color extends PDStyles_Extension_Observer {
	
	function __construct( $args = array(), Scaffold_Extension_Observable $observable = null ) {
		parent::__construct( $args, $observable );
		
		$this->keywords = array(
			'background-color',
			'bgc',
			'color',
			'c',
			'border-color',
			'bordc',
		);
	}
	
	/**
	 * Output in CSS for method css_*
	 * 
	 * @since 0.1.3
	 * @return string
	 **/
	function css_color() {
		extract($this->values);

		if ( empty( $color ) ) return '';
		return "color:$color;";
	}
	
	/**
	 * Output in CSS for method css_*
	 * 
	 * @since 0.1.3
	 * @return string
	 **/
	function css_background_color() {
		extract($this->values);
		
		if ( empty( $color ) ) return '';
		return "background-color:$color;";
	}
	
	/**
	 * Output in CSS for method css_*
	 * 
	 * @since 0.1.3
	 * @return string
	 **/
	function css_border_color() {
		extract($this->values);
		
		if ( empty( $color ) ) return '';
		return "border-color:$color;";
	}
	
	/**
	 * Return value for output in form element
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function form_value( $key ) {
		return trim( $this->values[ $key ], '# ');
	}
	
	/**
	 * Set variable with correct formatting
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function set( $variable, $values, $context = 'default' ) {
		$value = trim( $values['color'], '# ');

		if ( !empty( $value ) ) {
			$this->values['color'] = '#'.$value;
		}else {
			$this->values['color'] = '';
		}
	}
	
	function output() {
		?>
		<tr class="pds_color"><th valign="top" scrope="row">
			<label for="<?php echo $this->form_id; ?>">
				<?php echo $this->label ?>
			</label>
		</th><td valign="top">
			<input class="pds_color_input" type="text" name="<?php echo $this->form_name ?>[color]" id="<?php echo $this->form_id ?>" value="<?php echo $this->value('form', 'color'); ?>" size="8" maxlength="8" />
		</td></tr>
		<?php
	}
	
	
} // END class 