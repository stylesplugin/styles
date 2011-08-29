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
		// Change color picker to http://www.digitalmagicpro.com/jPicker/
		?>
		<tr class="pds_color"><th valign="top" scrope="row">
			<label for="<?php echo $this->form_id; ?>">
				<?php echo $this->label ?>
			</label>
		</th><td valign="top">
			<?php $this->output_inner() ?>
		</td></tr>
		<?php
	}
	
	function output_inner() {
		?>
		<input class="pds_color_input" type="text" name="<?php echo $this->form_name ?>[color]" id="<?php echo $this->form_id ?>" value="<?php echo $this->value('form', 'color'); ?>" size="8" maxlength="8" />
		<?php
	}
	
	
} // END class 