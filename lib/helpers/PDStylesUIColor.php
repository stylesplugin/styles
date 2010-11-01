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
	var $label;
	
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
		
		$args['default'] = trim( $args['default'], '# ');
		$args['value']   = trim( $args['value'], '# ');
		
		$this->id = $args['id'];
		$this->label = $args['label'];
		$this->default = $args['default'];
		$this->value = ( empty( $args['value'] ) ) ? $args['default'] : $args['value'];
	}
	
	function output() {
		?>
		<div class="pds_color">
			<input class="pds_color_input" type="text" name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>" value="<?php echo $this->value; ?>" size="8" maxlength="8" />
			<label for="<?php echo $this->id; ?>">
				<?php echo $this->label ?>
			</label>
		</div>
		<?php
	}
	

} // END class 