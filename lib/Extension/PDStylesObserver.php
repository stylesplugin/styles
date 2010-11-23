<?php

/**
 * Scaffold_Observer
 *
 * Observer class to implement the observer method
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
abstract class PDStyles_Extension_Observer extends Scaffold_Extension_Observer
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
	 * Default value of the form element
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $default;
	
	/**
	 * Array of values loaded from database
	 * 
	 * @since 0.1
	 * @var array
	 **/
	var $values;
	
	/**
	 * Variable type specified in CSS
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $type;
	
	/**
	 * Variable values to match this object to
	 * @since 0.1
	 * @var array
	 */
	var $keywords = array();

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
		$this->label = $args['label'];
		$this->type = $args['type'];
		
		$this->form_name = "{$args['form_name']}[$this->key]";
		$this->form_id = 'pds_'.md5( $this->form_name );
	}
	
	/**
	 * Detect if input CSS var looks like the type this object handles
	 * 
	 * @since 0.1
	 * @return bool
	 **/
	function is_type( $args ) {
		if ( in_array( $args['type'], $this->keywords ) ) return true;
		
		if (empty($this->keywords)) {
			FB::error('$this->keywords is empty in '.__CLASS__.'. Please set on __construct.');
		}
		
		return false;
	}
	
	/**
	 * Get value with correct formatting
	 * 
	 * @since 0.1
	 * @return string
	 **/
	function value( $context = null ) {
		
		$method = $context.'_value';
		if ( method_exists( $this, $method ) ) {
			return $this->$method();
		}else {
			return $this->values;
		}
	}
	
	abstract function set( $variable, $value, $context = 'default' );
	abstract function output();
	abstract function form_value();
	abstract function css_value();
	
	
}