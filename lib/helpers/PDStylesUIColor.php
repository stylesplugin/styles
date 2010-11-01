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
	var $nicename;
	
	/**
	 * Default value of the form element
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $default;
	
	function __construct( $args = array() ) {
		$defaults = array(
			// 'default'		=> '',
		);
		$args = wp_parse_args( $args, $defaults );
		
		$this->id = $args['id'];
		$this->nicename = $args['nicename'];
		$this->default = $args['default'];
	}
	
	function output() {
		?>
		<div class="pds_color">
			<input class="pds_color_input" type="text" name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>" value="<?php echo trim($this->default, '# '); ?>" size="8" maxlength="8" />
			<label for="<?php echo $this->id; ?>">
				<?php echo $this->nicename ?>
			</label>
		</div>
		<?php
	}
	

} // END class 