<?php

/**
 * StormStyles_Extension_Observer
 *
 * Observer class to implement the observer method
 * 
 * @package 		pdstyles
 * @author 			Paul Clark <pdclark@pdclark.com>
 * @copyright 		2009-2010 Paul Clark. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 */
abstract class StormStyles_Extension_Observer extends Scaffold_Extension_Observer
{
	/**
	 * Form element ID
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $form_id;
	
	/**
	 * Form element name for DB insert
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $form_name;
	
	/**
	 * Variable key in array
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $key;
	
	/**
	 * Nice text name for display in element label
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $label;
	
	/**
	 * Array of values loaded from database
	 * 
	 * @since 0.1
	 * @var array
	 **/
	var $values;
	
	/**
	 * Settings group key to display in
	 * 
	 * @var string
	 **/
	var $group;
	
	/**
	 * Arguments passed from CSS
	 * 
	 * @since 0.1
	 * @var array
	 **/
	var $args = array();

	/**
	 * Attaches the observer to the observable class
	 *
	 * @param $observer
	 * @access public
	 * @return void
	 */
	public function __construct( $args = array(), Scaffold_Extension_Observable $observable = null)
	{
		if ( !is_null($observable)) {
			parent::construct( $observable );
		}
		
		$defaults = array(
			// 'default'		=> '',
		);
		$args = wp_parse_args( $args, $defaults );
		
		$this->key = $args['key'];
		$this->group = $args['group'];
		$this->label = $args['label'];
		$this->method = $args['method'];
		
		$this->form_name = "{$args['form_name']}[$this->key]";
		$this->form_id = 'pds_'.md5( $this->form_name );
		
		unset( $args['method'], $args['key'], $args['label'], $args['form_name'] );
		
		$this->args = $args;
		
		if (!empty( $this->args ) && function_exists('add_settings_field') ) {
			add_settings_field(
	           $this->key,   // Unique ID
	           $this->label, // Label
	           array($this, 'output'), // Display callback
	           'StormStyles_Settings', // Form page
	           $this->group     // Form section
			);
		}
	}
	
	/**
	 * Get value with correct formatting
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function value( $context = null, $key = null ) {

		$css_method = $this->method;
		if ($context == 'css' && method_exists( $this, $css_method ) ) {
			return $this->$css_method();
		}
		
		$method = $context.'_value';
		if ( method_exists( $this, $method ) ) {
			return $this->$method( $key );
		}else {
			return $this->values;
		}
	}
	
	/**
	 * Return value for output in form element
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function form_value($key = null) {
		return $this->values[ $key ];
		
		// Reconsider to allow blank values -- perhaps only load CSS if object doesn't exist in DB
		// if ( ! empty( $this->values[ $key ] ) ) {
		// 	return $this->values[ $key ];
		// }else if ( is_array( $this->args['value'] ) && !empty( $this->args['value'][$key] )){
		// 	return $this->args['value'][$key];
		// }else {
		// 	return $this->args['value'];
		// }
	}
	
	abstract function set( $variable, $value, $context = 'default' );
	abstract function output();
	
	
}