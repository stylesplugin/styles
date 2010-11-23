<?php

/**
 * Attaches a color picker to variables with a hex color format
 * 
 * @since 0.1
 * @package pd-styles
 * @author pdclark
 **/
class PDStyles_Extension_Image extends PDStyles_Extension_Observer {
	
	function __construct( $args = array(), Scaffold_Extension_Observable $observable = null ) {
		parent::__construct( $args, $observable );
		
		$this->keywords = array(
			'image',
			'image-replace',
			'background-image',
		);
	}
	
	/**
	 * Return value for output in form element
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function form_value() {
		return $this->values['url'];
	}
	
	/**
	 * Return value for output in CSS
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function css_value() {
		
		if (empty($this->values)) return '';
		
		switch( $this->type ) {
			case 'image-replace':
			case 'image':
				$output = "image-replace: url({$this->values['url']});";
				break;
			case 'background-image':
				$output = "background-image: url({$this->values['url']});";
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
	function set( $variable, $values, $context = null ) {

		$uploads = wp_upload_dir();
		
		// Get real uploads path, including multisite blogs.dir
		if( defined('UPLOADS') ) {
			$values['url'] = str_replace( $uploads['baseurl'].'/', '/'.UPLOADS, $values['url']);
		}
		// Convert URL to path
		$values['url'] = str_replace( site_url(), '', $values['url']);
		
		$this->values['url'] = $values['url'];
		
	}
	
	function output() {	
		$value = $this->value('form');
		$hidden = empty( $value ) ? 'hidden ' : '';
		?>
		
		<tr class="pds_image"><th valign="top" scrope="row">
			<label for="<?php echo $this->form_id; ?>">
				<?php echo $this->label ?>
			</label>
			
		</th><td valign="top">	
			
			<a class="current thickbox <?php echo $hidden ?>image_thumb" href="<?php echo $this->value('form') ?>">
				<img style="height:80px;" src="<?php echo $this->value('form') ?>" alt="" /><br/>
			</a>

			<input class="pds_image_input" type="text" name="<?php echo $this->form_name ?>[url]" id="<?php echo $this->form_id ?>" value="<?php echo $this->value('form'); ?>" size="8" />
			<input type="button" class="button" value="<?php _e('Select Image') ?>" onclick="show_image_uploader('<?php echo $this->form_id ?>');"/>

			<?php if (!empty( $this->description )) : ?>
				<br/><small><?php echo $this->description ?></small>
			<?php endif; ?>
			
		</td></tr>
		<?php		
	}
	
} // END class 