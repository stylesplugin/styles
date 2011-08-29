<?php

/**
 * Format and iterate variable groups
 * 
 * @since 0.1
 * @package pd-styles
 * @author pdclark
 **/
class PDStyles_Extension_Group extends Scaffold_Extension_Observer {
	
	/**
	 * Form element ID and Name
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $id;
	
	/**
	 * Nice text name for display in element label
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $label;
	
	/**
	 * Variable group name used in the CSS
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $key;
	
	/**
	 * Variable objects in this group
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $variables;
	
	function __construct( $args = array() ) {
		if ( empty($args) ) return;

		$this->key = $args['key'];
		$this->label = ( empty( $args['label'] ) ) ? $args['key'] : $args['label'];
		$this->form_name = "{$args['form_name']}[$this->key]";
		
		unset( $args['label'], $args['key'] );
		
		$this->create_objects( $args );
	}
	
	function create_objects( $variables ) {
		global $PDStylesController;
		$controller = & $PDStylesController;
		
		// Instantiate Objects
		foreach ( $variables as $key => $args ) {
			if ( is_array($args) ) {
				foreach ( $controller->extensions as $ext ){

					if ( is_a( $ext, $args['class'] ) ) {
						$ext_class = get_class($ext);
					
						$args['key'] = $key;
						$args['form_name'] = $this->form_name;
					
						$this->variables[ $key ] = new $ext_class( $args );
					}
				
				}
			}
		}

		// Remove anything that wasn't recognised as an object
		foreach ( (array) $this->variables as $key => $object ) {
			if ( !is_object( $object ) ) {
				unset( $this->variables[ $key ] );
			}
		}

	}
	
	function output( ) {
		$id = 'pds_'.md5($this->form_name).$this->key;
		?>
		<div id="<?php echo $id; ?>">
			<h3><?php echo $this->label; ?></h3>
			<table class="form-table">
			<?php 
			foreach ( $this->variables as $variable ) {
				$variable->output();
			}
			?>
			</table>
			
			<p class="submit">
				<input class="pds-submit button-primary" type="submit" value="<?php _e('Save Changes'); ?>" />
				
				<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" /> 
				<span class="response"> </span>
			</p>
		</div>
		<?php
	}
	
	/**
	 * Update values of all children
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function set( $values ) {
		if ( !array_key_exists( $this->key, $values )) {
			FB::error($this->key, 'Key not found');
			return;
		}

		foreach ($this->variables as $variable) {
			$variable->set( 'value', $values[ $this->key ][ $variable->key ] );
		}
	}
	
	/**
	 * Recursively get all child values for CSS
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function get( $context = null ) {
		$values = array();
		foreach ($this->variables as $variable) {
			$values[ $variable->key ] = $variable->value($context);
		}
		return $values;
	}

} // END class 