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
		?>
		
		<tr class="pds_image"><th valign="top" scrope="row">
			<label for="<?php echo $this->form_id; ?>">
				<?php echo $this->label ?>
			</label>
			
		</th><td valign="top">	
			
			<?php $this->output_inner(); ?>
			
		</td></tr>
		<?php		
	}
	
	function output_inner() {
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