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
	 * Default value of the form element
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $default;
	
	function __construct( $args = array() ) {
		$defaults = array(
			'default'		=> '',
			'nice_name'		=>	'Untitled Slider',
		);
		$args = wp_parse_args( $args, $defaults );
		
		$this->id = $args['id'];
		$this->nice_name = $args['nice_name'];
		$this->default = $args['default'];
	}
	
	function output() {
		?>
		<div class="pds_color">
			<label for="<?php echo $this->id; ?>">
				<?php echo $this->nice_name ?>:
			</label>
			<input class="pds_color_input" type="text" name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>" value="<?php echo $this->default; ?>" size="8" maxlength="8" />
		</div>
		<?php
	}
	

} // END class 