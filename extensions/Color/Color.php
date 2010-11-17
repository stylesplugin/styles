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
	 * Variable key in array
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $key;
	
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
	
	/**
	 * Value loaded from database
	 * 
	 * @since 0.1
	 * @var string
	 **/
	private $value;
	
	/**
	 * Variable type specified in CSS
	 * 
	 * @since 0.1
	 * @var string
	 **/
	private $type;
	
	/**
	 * Variable values to match this object to
	 * @since 0.1
	 * @var array
	 */
	private $keywords = array (
		'background-color',
		'bgc',
		'color',
		'c',
		'border-color',
		'bordc',
	);
	
	function __construct( $args = array() ) {
		$defaults = array(
			// 'default'		=> '',
		);
		$args = wp_parse_args( $args, $defaults );
		
		$this->id = $args['id'];
		$this->key = $args['key'];
		$this->label = $args['label'];
		$this->type = $args['type'];
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
	
	/**
	 * Detect if input CSS var looks like the type this object handles
	 * 
	 * @since 0.1
	 * @return bool
	 **/
	function is_type( $args ) {
		if ( in_array( $args['type'], $this->keywords ) ) return true;
		return false;
	}
	

} // END class 