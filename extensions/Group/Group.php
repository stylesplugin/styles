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
		
		unset( $args['label'], $args['key'] );
		
		$this->create_objects( $args );
	}
	
	function create_objects( $variables ) {
		global $PDStylesAdminController;

		foreach ( $variables as $key => $args ) {
			if ( is_array($args) ) {
				foreach ( $PDStylesAdminController->extensions as $ext ){
					
					if ( $ext->is_type( $args ) ) {
						$ext_class = get_class($ext);
						$this->variables[ $key ] = new $ext_class( $args );
					}
					
				}
			}
		}

		// Remove anything that wasn't an object
		foreach ( (array) $this->variables as $key => $object ) {
			if ( !is_object( $object ) ) {
				unset( $this->variables[ $key ] );
			}
		}
		
	}
	
	function output() {

		echo '<h2>'.$this->label.'</h2>';
		
		foreach ( $this->variables as $variable ) {
			$variable->output();
		}
		
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