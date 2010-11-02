<?php

/**
 * Attaches a color picker to variables with a hex color format
 * 
 * @since 0.1
 * @package pd-styles
 * @author pdclark
 **/
class PDStyles_Extension_Color extends Scaffold_Extension_Observer {
	
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
	
	/**
	 * Detect if input CSS var looks like the type this object handles
	 * 
	 * @since 0.1
	 * @return bool
	 **/
	function is_type( $args ) {
		if ( $args['type'] == 'color' ) return true;
		
		$pattern = '/^#?([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$/';
		
		if ( preg_match( $pattern, trim( $args['default'] ) ) !== 0 ) {
			return true;
		}
		
		return false;
	}
	

} // END class 