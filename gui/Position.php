<?php

/**
 * Attaches a position picker to variables with a hex position format
 * 
 * @since 0.1
 * @package StormStyles
 * @author pdclark
 **/
class StormStyles_Extension_Position extends StormStyles_Extension_Observer {
	
	function __construct( $args = array(), Scaffold_Extension_Observable $observable = null ) {
		parent::__construct( $args, $observable );
		
		$this->args['min'] = (empty($this->args['min'])) ? 1 : $this->args['min']+1;
		$this->args['max'] = (empty($this->args['max'])) ? 201 : $this->args['max']+1;

		if ( $this->args['unit'] == 'none' || $this->args['unit'] == 'null' ) {
			$this->args['unit'] = '';
		}else if ( empty( $this->args['unit'] ) ) {
			$this->args['unit'] = 'px';
		}
	}
	
	/**
	 * Output in CSS for method css_*
	 * 
	 * @since 0.1.3
	 * @return string
	 **/
	function css_slider() {
		@extract($this->values);
		@extract($this->args);

		$output = $value.$unit;

		return $output;

	}
	
	/**
	 * Return value for output in form element
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function form_value() {
		@extract( $this->values );
		
		if ( empty($value) ) $value = 0;

		return $value;
	}
	
	/**
	 * Set variable with correct formatting
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function set( $variable, $values, $context = 'default' ) {
		@extract( $values );
		
		$this->values['value'] = preg_replace( '/[^0-9.-]/', '', $value ); // numbers only

	}
	
	function output() {
		@extract( $this->values );
		@extract( $this->args );
		
		?>
			<div>
				<input class="pds_position_input slider" type="text" data-min="<?php echo $min; ?>" data-max="<?php echo $max; ?>" name="<?php echo $this->form_name ?>[value]" id="<?php echo $this->form_id ?>" value="<?php echo $this->value('form'); ?>" size="4" maxlength="8" /> <?php echo $unit ?>
			</div>
		<?php
	}
	
	
} // END class