<?php

/**
 * Color User Interface Type
 * 
 * @since 0.1
 * @package pd-styles
 * @author pdclark
 **/
class PDStylesUIColor {
	
	/**
	 * Form element ID and Name
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $id;
	
	/**
	 * Nice text name for display in element label
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $nice_name;
	
	/**
	 * Value of the form element
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $value;
	
	function __construct( $args = array() ) {
		$defaults = array(
			'min'			=>	0,
			'max'			=>	50,
			'id'			=>	rand(1,1000),
			'nice_name'		=>	'Untitled Slider',
		);
		
		$args = wp_parse_args( $args, $defaults );
	}
	
	function output() {
		?>
		<div class="pds_color">
			<label for="<?php echo $this->id; ?>">
				<?php echo $this->nice_name ?>:
			</label>
			<input class="pds_color_input" type="text" name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>" value="<?php echo $this->value; ?>" size="8" maxlength="8" />
		</div>
		<?php
	}
	

} // END class 