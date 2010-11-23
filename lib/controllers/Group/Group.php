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
		global $PDStylesAdminController;

		// Instantiate Objects
		foreach ( $variables as $key => $args ) {
			if ( is_array($args) ) {
				foreach ( $PDStylesAdminController->extensions as $ext ){
					
					if ( $ext->is_type( $args ) ) {
						$args['key'] = $key;
						$ext_class = get_class($ext);
						
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
		<div id="<?php echo $id; ?>" class="postbox">
			<div class="handlediv" title="Click to toggle"><br /></div>
			<h3 class="hndle"><span><?php echo $this->label; ?></span></h3>
			<div class="inside">
				<table class="form-table">
				<?php 
				foreach ( $this->variables as $variable ) {
					$variable->output();
				}
				?>
				</table>
			</div>
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
	
	/**
	 * Detect if input CSS var looks like the type this object handles
	 * 
	 * @since 0.1
	 * @return bool
	 **/
	function is_type( $args ) {
		// Never match child elements to Group
		return false;
	}
	

} // END class 