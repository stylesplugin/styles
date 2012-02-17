<?php

/**
 * Get CSS variables from Scaffold, iterate down the tree
 * 
 * @since 0.1
 * @package StormStyles
 * @author pdclark
 **/
class StormStyles_Extension_Variable extends Scaffold_Extension_Observer {
	
	/**
	 * Loaded SCSS file path
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $file;
	
	/**
	 * Where to save compiled CSS
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $cache_file;
	
	/**
	 * Path to CSS in a form appropriate for use as an array key
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $permalink;
	
	/**
	 * Variables loaded from CSS
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $variables;
	
	/**
	 * Container for the scaffold object
	 * 
	 * @since 0.1
	 * @var string
	 **/
	var $scaffold;
	
	function __construct( $args = array(), $styles ) {
		if ( empty( $args['path'] ) ) {
			FB::error('No file specified in '.__FILE__);
			return false;
		}
		
		$this->file = $args['path'];
		$this->cache_file = $args['cache_path'];
		
		$this->path = $args['path'];
		$this->uri = $args['path'];
		$this->cache_path = $args['cache_path'];
		$this->cache_uri = $args['cache_path'];

		$this->variables = $styles->css->wp_bridge->found;

		foreach( $this->variables as $key => &$group ) {

			$group['form_name'] = "variables";
			$group['key'] = $key;
			$group = new StormStyles_Extension_Group( $group );
			
			// Remove empty groups
			if ( empty( $group->variables ) ) {
				unset( $this->variables[$key] );
			}
		}
		
		return true;
	}
	
	/**
	 * Run on serialize (before inserting into DB)
	 * 
	 * @return array of vars to serialize
	 **/
	function __sleep() {
		return array(
			'file',
			'cache_file',
			'permalink',
			'variables',
		);
	}
	
	/**
	 * Detect functions/arguments. Convert to Array.
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function variables_cleanup() {
		$tmp = array();
		
		foreach ( $this->variables as $group => &$variables ) {
			
			// Tell children what the parent key is, for building form names
			$this->variables[ $group ]['key'] = $group;
			
			foreach ( $variables as $key => &$value ) {
				if ( $this->is_protected_key( $key ) ) { continue; }
				
				$found = preg_match('/
						(^[^\(\\s]*)\\s*\(  # 1 function_name (
						[\'"\\s]?           # maybe quote or space
						([^\'\"\)]*)        # 2 = arguements
						[\'"\\s]?           # maybe end quote or space
						(?:\\s*\\))?        # maybe )
					/xs',
					$value,
					$match
				);

				if (!$found) {
					continue; 
				}
				
				$args = array(
					'method' => 'css_'.str_replace( '-', '_', $match[1] ),
					'key' => $group,
					'label' => $key,
				);
				
				$tmp_args = $match[2];

				// Extract Arguments into key=>value array
				$tmp_args = explode(',', $tmp_args);			
				foreach( $tmp_args as $tmp_val ) {

					$tmp_val = explode('=', $tmp_val);

					$arg_key = trim( $tmp_val[0] );
					$arg_val = trim( $tmp_val[1] );

					$args[ $arg_key ] = $arg_val;
				}
				
				$value = $args;
		
			}
		}
		unset($tmp);
	}
	
	function is_protected_key( $key ) {
		$protected = array(
			'key',
			'label',
		);
		
		return in_array( strtolower($key), $protected );
		
	}
	
	function output() {
		foreach ($this->variables as $variable) {
			$variable->output();
		}
	}
	
	/**
	 * Update values of all children
	 * 
	 * @since 0.1
	 * @return void
	 **/
	function set( $values ) {
		if ( empty( $values ) ) {
			return;
		}
		//if ( !array_key_exists( $this->permalink, (array) $values )) {
		//	FB::error('$this->permalink not found in $values.');
		//	FB::error($this->permalink, '$this->permalink');
		//	FB::error($values, '$values');
		//	FB::error(debug_backtrace(), 'debug_backtrace()');
		//	return;
		//}
		
		foreach ($this->variables as $variable) {
			$variable->set( $values );
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
			$values[ $variable->key ] = $variable->get( $context );
		}
		return $values;
	}	

} // END class 