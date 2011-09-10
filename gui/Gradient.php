<?php

/**
 * Attaches a gradient picker to variables with a hex gradient format
 * 
 * @since 0.1
 * @package StormStyles
 * @author pdclark
 **/
class StormStyles_Extension_Gradient extends StormStyles_Extension_Observer {
	
	function __construct( $args = array(), Scaffold_Extension_Observable $observable = null ) {
		parent::__construct( $args, $observable );
	}
	
	/**
	 * Set variables with correct formatting
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function set( $variable, $input, $context = 'default' ) {
		if ( empty( $input ) ) {
			$this->values = array();
			return;
		}
		
		$this->values['stops'] = $input['stops'];
	}
	
	function output() {
		?>
		<div class="gradPicker">
			<label>Stops: <input class="pds_text_input stops" type="text" name="<?php echo $this->form_name ?>[stops]" id="<?php echo $this->form_id ?>" value="<?php echo $this->value('form', 'stops'); ?>" size="32" /></label>
		</div>
		<?php
	}
	
} // END class 