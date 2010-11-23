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
		
		$this->keywords = array(
			'background-color',
			'bgc',
			'color',
			'c',
			'border-color',
			'bordc',
		);
	}
	
	/**
	 * Return value for output in form element
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function form_value() {
		return trim( $this->value, '# ');
	}
	
	/**
	 * Return value for output in CSS
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function css_value() {
		
		if (empty($this->value)) return '';
		
		switch( $this->type ) {
			case 'bgc':
			case 'background-color':
				$output = "background-color:{$this->value};";
				break;
				
			case 'c':
			case 'color':
				$output = "color:{$this->value};";
				break;
			
			case 'border-color':
			case 'bordc':
				$output = "border-color:{$this->value};";
				break;
			
		}
	
		return $output;
		
	}
	
	/**
	 * Set variable with correct formatting
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function set( $variable, $value, $context = 'default' ) {

		$value = trim( $value, '# ');
		
		if ( !empty( $value ) ) {
			$this->value = '#'.$value;
		}else {
			$this->value = '';
		}
	}
	
	function output() {
		?>
		<tr class="pds_color"><th valign="top" scrope="row">
			<label for="<?php echo $this->form_id; ?>">
				<?php echo $this->label ?>
			</label>
		</th><td valign="top">
			<input class="pds_color_input" type="text" name="<?php echo $this->form_name ?>" id="<?php echo $this->form_id ?>" value="<?php echo $this->value('form'); ?>" size="8" maxlength="8" />
		</td></tr>
		<?php
	}
	
	
} // END class 