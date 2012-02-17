<?php

/**
 * Format and iterate variable groups
 * 
 * @since 0.1
 * @package StormStyles
 * @author pdclark
 **/
class StormStyles_Extension_Group extends Scaffold_Extension_Observer {
	
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
		
		if ( function_exists('add_settings_section')  ) {
			add_settings_section(
	            $this->key, // Unique ID 
	            $this->label, // Label
	            null, //array('DemoPlugin', 'Overview'), // Description callback
	            'StormStyles_Settings' // Page
			);
		}
		
		unset( $args['label'], $args['key'] );
		
		$this->create_objects( $args );
	}
	
	function create_objects( $variables ) {
		global $StormStylesController;
		
		// Instantiate Objects
		foreach ( $variables as $key => $args ) {

			if ( is_array($args) ) {

				foreach ( $StormStylesController->extensions as $ext ){

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