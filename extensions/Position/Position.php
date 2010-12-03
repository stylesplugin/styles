<?php

/**
 * Attaches a position picker to variables with a hex position format
 * 
 * @since 0.1
 * @package pd-styles
 * @author pdclark
 **/
class PDStyles_Extension_Position extends PDStyles_Extension_Observer {
	
	function __construct( $args = array(), Scaffold_Extension_Observable $observable = null ) {
		parent::__construct( $args, $observable );
		
		$this->keywords = array(
			'position',
			'direction',
			'unit',
			'min',
			'max',
		);
	}
	
	/**
	 * Output in CSS for method css_*
	 * 
	 * @since 0.1.3
	 * @return string
	 **/
	function css_slider() {
		extract($this->values);
		
		$position = $this->args['position']; //eg padding
		$direction = $this->args['direction']; //eg right
		$unit = $this->args['unit']; //eg px
		$amount = $this->values['position'];
		
		if (
				!empty($position)
				&&!empty($direction)
				&&!empty($unit)
				&&!empty($amount)
			){
			
			$output = "$position-$direction: $amount$unit;";

		}
		return $output;

	}
	
	/**
	 * Return value for output in form element
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function form_value() {
		$value = ( empty($this->values['position']) ) ? 0 : $this->values['position'];
		return $value;
	}
	
	/**
	 * Set variable with correct formatting
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function set( $variable, $values, $context = 'default' ) {
		$value = $values['position'];

		if ( !empty( $value ) ) {
			$this->values['position'] = $value;
		}else {
			$this->values['position'] = '';
		}
	}
	
	function output() {
		extract($this->values);
		$min = $this->args['min'];
		$max = $this->args['max'];
		
		if ((empty($min)) || (empty($max))){
			$min=5;
			$max=55;
		}
		
		?>
		<tr class="pds_position"><th valign="top" scrope="row">
			<label for="<?php echo $this->form_id; ?>">
				<?php echo $this->label ?>
			</label>
		</th><td valign="top">
			
			<input class="pds_position_input" type="text" data-min="<?php echo $min; ?>" data-max="<?php echo $max; ?>" name="<?php echo $this->form_name ?>[position]" id="<?php echo $this->form_id ?>" value="<?php echo $this->value('form'); ?>" size="8" maxlength="8" /> <?php echo $this->args['unit']; ?>
			<div style="width: 200px;"></div> <?php /* Container for jquery slider */ ?>
		</td></tr>
	
		<?php
	}
	
	
} // END class