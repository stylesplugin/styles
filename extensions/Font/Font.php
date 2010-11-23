<?php

/**
 * Attaches a font picker to variables with a hex font format
 * 
 * @since 0.1
 * @package pd-styles
 * @author pdclark
 **/
class PDStyles_Extension_Font extends PDStyles_Extension_Observer {
	
	function __construct( $args = array(), Scaffold_Extension_Observable $observable = null ) {
		parent::__construct( $args, $observable );
		
		$this->keywords = array(
			'font',
		);
	}
	
	/**
	 * Return value for output in form element
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function form_value($key = null) {
		return $this->values[$key];
	}
	
	/**
	 * Return value for output in CSS
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function css_value() {
		
		$output = '';
		extract($this->values);
		
		switch( $this->type ) {
			case 'font':
				if (!empty($font_size)) $output .= "font-size:{$font_size}px;";
				if (!empty($font_family)) $output .= "font-family:{$font_family};";
				break;
		}
	
		return $output;
		
	}
	
	/**
	 * Set variables with correct formatting
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function set( $variable, $input, $context = 'default' ) {
		if ( empty( $input ) ) {
			$this->values = array();
			return;
		}
		
		$this->values['font_size'] = preg_replace('/[^0-9]/', '', $input['font_size'] ); // Numbers only

		if ( in_array( $input['font_family'], $this->families() ) ) {
			$this->values['font_family'] = $input['font_family'];
		}
	}
	
	function output() {
		$font_family = $this->value('form', 'font_family');
		?>
		<tr class="pds_font"><th valign="top" scrope="row">
			<label for="<?php echo $this->form_id; ?>">
				<?php echo $this->label ?>
			</label>
		</th><td valign="top">
			<input name="<?php echo $this->form_name ?>[font_size]" class="pds_font_input" type="text" id="<?php echo $this->form_id ?>_font_size" value="<?php echo $this->value('form', 'font_size'); ?>" size="2" maxlength="4" />px
			
			<select name="<?php echo $this->form_name ?>[font_family]" class="pds_font_select">
				<option value="">Font Family</option>
				<?php foreach ($this->families() as $name => $value ) :?>
				<option value='<?php echo $value?>' <?php if ( $value == $font_family ) echo 'selected'; ?> ><?php echo $name ?></option>
				<?php endforeach; ?>
			</select>
		
		</td></tr>
		<?php
	}
	
	function families() {
		return array(
			'Arial'				=>	'Arial, Helvetica, sans-serif',
			'Times'				=>	'Times, Georgia, serif',
			'Verdana'			=>	'Verdana, Tahoma, sans-serif',
			'Century Gothic'	=>	'"Century Gothic", Helvetica, Arial, sans-serif',
			'Helvetica'			=>	'Helvetica, Arial, sans-serif',
			'Georgia'			=>	'Georgia, Times, serif',
			'Lucida Grande'		=>	'"Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana,sans-serif',
			'Palatino'			=>	'Palatino, Georgia, serif',
			'Garamond' 			=>	'Garamond, Palatino, Georgia, serif',
			'Tahoma'   			=>	'Tahoma, Verdana, Helvetica, sans-serif',
			'Courier'  			=>	'Courier, monospace',
			'Trebuchet MS'		=>	'"Trebuchet MS", Tahoma, Helvetica, sans-serif',
			'Comic Sans MS'		=>	'"Comic Sans MS", Arial, sans-serif',
			'Bookman'			=>	'Bookman, Palatino, Georgia, serif',
			
		);
	}
	
	
} // END class