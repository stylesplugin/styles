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
		<input class="pds_color_input" type="text" name="<?php echo $this->form_name ?>[from]" id="<?php echo $this->form_id ?>" value="<?php echo $this->value('form', 'from'); ?>" size="8" maxlength="8" />
		<input class="pds_color_input" type="text" name="<?php echo $this->form_name ?>[to]" id="<?php echo $this->form_id ?>" value="<?php echo $this->value('form', 'to'); ?>" size="8" maxlength="8" />
		<div><input class="pds_text_input slider" type="text" name="<?php echo $this->form_name ?>[size]" id="<?php echo $this->form_id ?>" value="<?php echo $this->value('form', 'size'); ?>" size="4" maxlength="8" />px</div>
		<?php
	}
	
} // END class 