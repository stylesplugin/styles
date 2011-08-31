<?php

/**
 * Attaches a gradient picker to variables with a hex gradient format
 * 
 * @since 0.1
 * @package pd-styles
 * @author pdclark
 **/
class PDStyles_Extension_Gradient extends PDStyles_Extension_Observer {
	
	function __construct( $args = array(), Scaffold_Extension_Observable $observable = null ) {
		parent::__construct( $args, $observable );
	
		$this->args['size'] = (empty($this->args['size'])) ? 1 : $this->args['size']+1;
	}
	
	/**
	 * Return value for output in form element
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function form_value( $key ) {
		switch ($key) {
			case 'from':
			case 'to':
				return trim( $this->values[ $key ], '# ');
				break;
			case 'size':
				if( empty( $this->values[ $key ] ) ) {
					return '0';
				}else {
					return preg_replace( '/[^0-9]/', '', $this->values[ $key ] ); // numbers only
				}
				break;
			default:
				return $this->values[ $key ];
				break;
		}
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
		
		$this->values['from'] 		= ( empty($input['from']) ) ? '' : '#'.trim( $input['from'], '# ');
		$this->values['to'] 		= ( empty($input['to']) ) ? '' : '#'.trim( $input['to'], '# ');
		$this->values['direction'] 	= $input['direction'];
		$this->values['size'] 		= $input['size'];
		
	}
	
	function output() {
		?>
		<tr class="pds_gradient"><th valign="top" scrope="row">
			<label for="<?php echo $this->form_id; ?>">
				<?php echo $this->label ?>
			</label>
		</th><td valign="top">
			<?php $this->output_inner() ?>
		</td></tr>
		<?php
	}
	
	function output_inner() {
		?>
		<div class="colors">
			<input class="pds_color_input grad_color" type="text" name="<?php echo $this->form_name ?>[from]" id="<?php echo $this->form_id ?>" value="<?php echo $this->value('form', 'from'); ?>" size="8" maxlength="8" />
			<input class="pds_color_input grad_color" type="text" name="<?php echo $this->form_name ?>[to]" id="<?php echo $this->form_id ?>" value="<?php echo $this->value('form', 'to'); ?>" size="8" maxlength="8" />
		</div>
		<div>Stops: <input class="pds_text_input stops" type="text" name="<?php echo $this->form_name ?>[stops]" id="<?php echo $this->form_id ?>" value="<?php echo $this->value('form', 'stops'); ?>" size="32" /></div>
		
		<style>
			.stop-markers, .preview {width: 500px;}
			.stop-markers { position:relative;;margin-top:5px;height: 80px;}
			.stop-markers .marker {position:absolute;top:50px;left:0;width: 500px;height: 0px;border:none;}
			.stop-markers .marker a {background-image:url();}
			.preview {height: 45px;display:block;border: 1px solid black;}
/*			.pds_gradient .colors {display:none;}*/
		</style>
		
		
		<div class="preview"></div>
		<div class="stop-markers">
			<div class="marker"></div>
			<div class="marker"></div>
		</div>
		<?php
	}
	
} // END class 