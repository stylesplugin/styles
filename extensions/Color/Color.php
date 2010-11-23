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
	 * Get variable with correct formatting
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function get( $variable, $context ) {
		$value = $this->$variable;

		switch( $context ) {
			case 'form':
			
				return trim( $value, '# ');
			
				break;
			
			case 'css':
				
				if (empty($value)) return '';
				
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
				
				break;
			
			default:
				return $value;
				break;
		}
	}
	
	/**
	 * Set variable with correct formatting
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function set( $variable, $value, $context = 'default' ) {

		switch( $context ) {
			
			default:
				$value = trim( $value, '# ');
				
				if ( !empty( $value ) ) {
					$this->value = '#'.$value;
				}else {
					$this->value = '';
				}
				
				break;
		}
	}
	
	function output( $permalink ) {
		$id = "{$permalink}[$this->key]";
		?>
		<tr class="pds_color"><th valign="top" scrope="row">
			<label for="<?php echo $id; ?>">
				<?php echo $this->label ?>
			</label>
		</th><td valign="top">
			<input class="pds_color_input" type="text" name="<?php echo $id ?>" id="<?php echo $id ?>" value="<?php echo $this->get('value', 'form'); ?>" size="8" maxlength="8" />
		</td></tr>
		<?php
	}
	
	
} // END class 