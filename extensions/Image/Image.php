<?php

/**
 * Attaches a color picker to variables with a hex color format
 * 
 * @since 0.1
 * @package pd-styles
 * @author pdclark
 **/
class PDStyles_Extension_Image extends Scaffold_Extension_Observer {
	
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
		'image',
		'image-replace',
		'background-image',
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
	
	function output( $permalink ) {
		$name = "{$permalink}[$this->key]";
		$id = 'pds_'.md5($name);	
		?>
		
		<tr class="pds_image"><th valign="top" scrope="row">
			<label for="<?php echo $id; ?>">
				<?php echo $this->label ?>
			</label>
			
		</th><td valign="top">	
			
			<a class="current thickbox <?php if (empty( $this->value )) echo 'hidden '?>image_thumb" href="<?php echo $this->get('value', 'form') ?>">
				<img style="height:80px;" src="<?php echo $this->get('value', 'form') ?>" alt="" /><br/>
			</a>

			<input class="pds_image_input" type="text" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $this->get('value', 'form'); ?>" size="8" maxlength="8" />
			<input type="button" class="button" value="<?php _e('Select Image') ?>" onclick="show_image_uploader('<?php echo $id ?>');"/>

			<?php if (!empty( $this->description )) : ?>
				<br/><small><?php echo $this->description ?></small>
			<?php endif; ?>
			
		</td></tr>
		<?php		
	}
	
	/**
	 * Get variable with correct formatting
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function get( $variable, $context = null ) {
		$value = $this->$variable;

		switch( $context ) {
			
			case 'css':
				
				if (empty($value)) return '';
				
				switch( $this->type ) {
					case 'image-replace':
					case 'image':
						$output = "image-replace: url({$this->value});";
						break;
					case 'background-image':
						$output = "background-image: url({$this->value});";
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
	function set( $variable, $value, $context = null ) {

		switch( $context ) {
			
			default:
				$value = str_replace( site_url(), '', $value);
				$this->value = $value;
				break;
		}
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