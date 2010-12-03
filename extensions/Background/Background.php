<?php

/**
 * Combination of all possible background elements
 * 
 * @since 0.1.4
 * @package pd-styles
 * @author pdclark
 **/
class PDStyles_Extension_Background extends PDStyles_Extension_Observer {
	
	/**
	 * Container for color object
	 * 
	 * @since 0.1.4
	 * @var PDStyles_Extension_Color
	 **/
	var $Color;
	
	/**
	 * Container for image object
	 * 
	 * @since 0.1.4
	 * @var PDStyles_Extension_Image
	 **/
	var $Image;
	
	/**
	 * Container for gradient object
	 * 
	 * @since 0.1.3
	 * @var PDStyles_Extension_Gradient
	 **/
	var $Gradient;
	
	function __construct( $args = array(), Scaffold_Extension_Observable $observable = null ) {
		parent::__construct( $args, $observable );

		if ( class_exists('PDStyles_Extension_Color') ) $this->Color = new PDStyles_Extension_Color( $args, $observable );
		if ( class_exists('PDStyles_Extension_Image') )  $this->Image = new PDStyles_Extension_Image( $args, $observable );
		if ( class_exists('PDStyles_Extension_Gradient') )  $this->Gradient = new PDStyles_Extension_Gradient( $args, $observable );
	}
	
	/**
	 * Output in CSS for method css_*
	 * 
	 * @since 0.1.4
	 * @return string
	 **/
	function css_background() {
		extract($this->values);
		
		$out = '';
		
		if ( $enable_Color ) $out .= $this->Color->css_background_color();
		if ( $enable_Image ) $out .= $this->Image->css_background_image();
		if ( $enable_Gradient ) $out .= $this->Gradient->css_background_gradient();
		
		return $out;
	}
	
	/**
	 * Set variable with correct formatting
	 * 
	 * @since 0.1.4
	 * @return string
	 **/
	function set( $variable, $values, $context = null ) {
		if ( !empty( $values['url'] )) { $this->Image->set( $variable, $values, $context ); }
		if ( !empty( $values['color'] )) { $this->Color->set( $variable, $values, $context ); }
		if ( !empty( $values['from'] )) { $this->Gradient->set( $variable, $values, $context ); }
		
		$this->values = @array_merge( $this->values, $this->Image->values, $this->Color->values, $this->Gradient->values );
		
		$this->values['enable_Color'] = $values['enable_Color'];
		$this->values['enable_Image'] = $values['enable_Image'];
		$this->values['enable_Gradient'] = $values['enable_Gradient'];
	}
	
	/**
	 * Return value for output in form element
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function form_value( $key ) {
		switch( $key ) {
			case 'color':
				return $this->Color->form_value( $key );
				break;
			case 'url':
				return $this->Image->form_value( $key );
				break;
			case 'from':
			case 'to':
			case 'direction':
			case 'size':
				return $this->Gradient->form_value( $key );
				break;
			default:
				return $this->values[ $key ];
				break;
		}
		
	}
	
	function output() {	
		$types = array( 'Image', 'Gradient', 'Color', );
		?>
		
		<tr class="pds_background"><th valign="top" scrope="row">
			<label for="<?php echo $this->form_id; ?>">
				<?php echo $this->label ?>
			</label>
			
		</th><td valign="top">	
			
			<div class="types">
				<?php foreach ( $types as $type ) : ?>
					<label><input type="checkbox" name="<?php echo $this->form_name ?>[enable_<?php echo $type ?>]" value="<?php echo $type ?>" <?php if ( $type == $this->value('form', 'enable_'.$type ) ) echo 'checked="checked"' ?> > <?php echo $type ?> </label>
				<?php endforeach; ?>
			</div>
			
			<?php foreach ( $types as $type ) : ?>
				<div class="pds_<?php echo $type; ?> <?php if ( $type !== $this->value('form', 'enable_'.$type ) ) echo 'hidden' ?>">
					<?php $this->$type->output_inner() ?>
				</div>
			<?php endforeach; ?>
			
		</td></tr>
		<?php		
	}
	
} // END class 