<?php

/**
 * Combination of all possible background elements
 * 
 * @since 0.1.4
 * @package StormStyles
 * @author pdclark
 **/
class StormStyles_Extension_Background extends StormStyles_Extension_Observer {
	
	/**
	 * Set variable with correct formatting
	 * 
	 * @since 0.1.4
	 * @return string
	 **/
	function set( $variable, $values, $context = null ) {

		$this->values['active'] = $values['active'];
		$this->values['css']    = $values['css'];
		$this->values['image']  = $values['image'];
		$this->values['color']  = $values['color'];
		$this->values['stops']  = $values['stops'];
		
	}
	
	/**
	 * Return value for output in form element
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function form_value( $key ) {
		switch( $key ) {
			case 'url':
				
				$uploads = wp_upload_dir();

				// Get real uploads path, including multisite blogs.dir
				if( defined('UPLOADS') ) { $values['url'] = str_replace( $uploads['baseurl'].'/', '/'.UPLOADS, $values['url']); }
				// Convert URL to path
				$values['url'] = str_replace( site_url(), '', $values['url']);

				$this->values['url'] = $values['url'];
				
				break;
			default:
				return $this->values[ $key ];
				break;
		}
		
	}
	
	function output() {
		global $StormStylesController;
		
		$rgba = $StormStylesController->css->wp_bridge->rgba_to_ahex( $this->value('form', 'color') );
		?>
			<div class="bgPicker">
				<div class="types">
					<a title="Image" href="#" data-type="image">Image</a>
					<a title="Gradient" href="#" data-type="gradient">Gradient</a>
					<a title="Color" href="#" data-type="color">Color</a>
					<a title="Transparent" href="#" data-type="transparent">Transparent</a>
					<a title="Hide" href="#" data-type="hide">Hide</a>
				</div>
			
				<div class="data">
					<label>Active <input type="text" name="<?php echo $this->form_name ?>[active]" value="<?php echo $this->value('form', 'active'); ?>" /></label>
					<label>CSS <input type="text" name="<?php echo $this->form_name ?>[css]" value="<?php echo $this->value('form', 'css'); ?>" /></label>
					<label>Image <input type="text" name="<?php echo $this->form_name ?>[image]" value="<?php echo $this->value('form', 'image');?>" id="<?php echo $this->form_id.'_image' ?>" /></label>
					<label>Stops <input type="text" name="<?php echo $this->form_name ?>[stops]" value="<?php echo $this->value('form', 'stops'); ?>" /></label>
					<label>Color <input type="text" name="<?php echo $this->form_name ?>[color]" value="<?php echo $this->value('form', 'color'); ?>" data-ahex="<?php echo $rgba['hexa'] ?>"/></label>
				</div>
				
				<div class="ui"></div>
			</div>
		<?php		
	}
	
} // END class 