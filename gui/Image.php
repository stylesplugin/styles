<?php

/**
 * Attaches a color picker to variables with a hex color format
 * 
 * @since 0.1
 * @package StormStyles
 * @author pdclark
 **/
class StormStyles_Extension_Image extends StormStyles_Extension_Observer {
	
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
		$value = $this->value('form', 'url');
		$hidden = empty( $value ) ? 'hidden ' : '';
		?>
		<a class="current thickbox <?php echo $hidden ?>image_thumb" href="<?php echo $this->value('form', 'url') ?>">
			<img style="height:80px;" src="<?php echo $this->value('form', 'url') ?>" alt="" /><br/>
		</a>

		<input class="pds_image_input" type="text" name="<?php echo $this->form_name ?>[url]" id="<?php echo $this->form_id ?>" value="<?php echo $this->value('form', 'url'); ?>" size="32" />
		<input type="button" class="button" value="<?php _e('Select Image') ?>" onclick="show_image_uploader('<?php echo $this->form_id ?>');"/>

		<?php if (!empty( $this->description )) : ?>
			<br/><small><?php echo $this->description ?></small>
		<?php endif; ?>
		<?php
	}
	
} // END class 
