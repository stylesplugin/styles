<?php

/**
 * Attaches a color picker to variables with a hex color format
 * 
 * @since 0.1
 * @package StormStyles
 * @author pdclark
 **/
class StormStyles_Extension_Color extends StormStyles_Extension_Observer {
	
	function __construct( $args = array(), Scaffold_Extension_Observable $observable = null ) {
		parent::__construct( $args, $observable );
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
		<input class="pds_color_input" type="text" name="<?php echo $this->form_name ?>[color]" id="<?php echo $this->form_id ?>" value="<?php echo $this->value('form', 'color'); ?>" size="8" maxlength="8" />
		<?php
	}
	
	
} // END class 