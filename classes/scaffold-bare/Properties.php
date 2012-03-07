<?php
/**
 * Scaffold_Extension_Properties
 *
 * Allows other extensions to register custom properties.
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Extension_Properties extends Scaffold_Extension
{	
	/**
	 * @var array
	 */
	public $properties = array();
	
	/**
	 * Lets extensions register custom functions by creating a hook
	 * @access public
	 * @param $source Scaffold_Source
	 * @return void
	 */
	public function initialize($source,$scaffold)
	{
		$scaffold->notify('register_property',array($this));
	}
	
	/**
	 * Register a new function
	 * @access public
	 * @param $name
	 * @param $map
	 * @return void
	 */
	public function register($name,$map)
	{
		$this->properties[$name] = $map;
	}
	
	/**
	 * Styles plugin version of post_process
	 * 
	 * @access public
	 * @param $source
	 * @return string
	 */
	public function styles_before_process( $styles )
	{
		// Go through each custom function
		foreach($this->properties as $name => $property)
		{
			$obj 	= $property[0];
			$method = $property[1];

			// Find them in the CSS
			foreach($styles->css->helper->find_properties($name,$styles->css->contents) as $found)
			{
				// Call the hook method for this function
				$result = call_user_func_array(array($obj,$method),array($found['value'], $styles->css, $found ));
				
				// Replace it in the CSS
				$styles->css->contents = str_replace($found['property'],$result,$styles->css->contents);
			}
		}
	}
	
	/**
	 * @access public
	 * @param $source
	 * @return string
	 */
	public function post_process($source,$scaffold)
	{
		// Go through each custom function
		foreach($this->properties as $name => $property)
		{
			$obj 	= $property[0];
			$method = $property[1];

			// Find them in the CSS
			foreach($scaffold->helper->css->find_properties($name,$source->contents) as $found)
			{
				// Call the hook method for this function
				$result = call_user_func_array(array($obj,$method),array($found['value'], $scaffold, $found ));
				
				// Replace it in the CSS
				$source->contents = str_replace($found['property'],$result,$source->contents);
			}
		}
	}
}