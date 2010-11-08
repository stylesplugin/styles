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
		$this->key = $args['key'];
		$this->label = $args['label'];
		$this->default = $args['default'];
		$this->value = ( empty( $args['value'] ) ) ? $args['default'] : $args['value'];
	}
	
	function output( $permalink ) {
		$name = "{$permalink}[$this->key]";
		$id = 'pds_'.md5($name);
		
		$row = array(
			'id' => 'touchbootimg',
			'label' => 'Boot image',
			'desc' => '320x460px png, jpg, gif file',
			'content' => '<input type="text" value="'.$options['touchbootimg'].'" name="touchbootimg" id="touchbootimg" class="text"/><input type="button" class="button" value="'.__('Select Image').'" onclick="show_image_uploader(\'touchbootimg\');"/>'
		);
		
		?>
		<div class="pds_image">
			<input class="pds_image_input" type="text" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $this->value; ?>" size="8" maxlength="8" />
			<input type="button" class="button" value="<?php _e('Select Image') ?>" onclick="show_image_uploader('<?php echo $id ?>');"/>
			<label for="<?php echo $id; ?>">
				<?php echo $this->label ?>
			</label>
			<?php if (!empty($this->description )) : ?>
				<br/><small><?php echo $this->description ?></small>
			<?php endif; ?>
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
		if ( $args['type'] == 'image' ) return true;
		if ( $args['default'] == 'image' ) return true;
		
		//	$pattern = '/^#?([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$/';
		//	
		//	if ( preg_match( $pattern, trim( $args['default'] ) ) !== 0 ) {
		//		return true;
		//	}
		
		return false;
	}
	

} // END class 