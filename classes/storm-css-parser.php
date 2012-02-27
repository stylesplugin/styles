<?php

/* 
	All the Scaffold stuff from Anthony Short's CSS Scaffold
	https://github.com/anthonyshort/scaffold
*/
require 'scaffold-bare/CSS.php';
require 'scaffold-bare/Extension.php';
require 'scaffold-bare/NestedSelectors.php';
require 'scaffold-bare/Properties.php';
require 'scaffold-bare/Compressor.php';
require 'storm-wp-bridge.php';

class StormCSSParser {
	
	var $path;
	var $helper;
	var $contents;
	var $original;
	var $wp_bridge;
	var $import_paths;
	var $styles;
	
	function __construct( $styles ) {
		$this->styles = $styles;
		
		// Load CSS source
		$this->contents = $this->original = file_get_contents( $styles->file_paths['path'] );

		// Where to search for embedded files. Used by background-replace
		$this->import_paths = array( get_stylesheet_directory(), $styles->wp->plugin_dir_path(), );
		
		// Init helper objects
		$this->helper->css = new Scaffold_Helper_CSS();
		$this->nested_selectors = new Scaffold_Extension_NestedSelectors();
		$this->wp_bridge = new StormWPBridge( $styles );
		$this->properties = new Scaffold_Extension_Properties();
		
		add_action( 'styles_before_process', array($this->nested_selectors, 'styles_before_process'), 10, 1 );
		
		add_action( 'styles_before_process', array($this->wp_bridge, 'before_process'), 10, 1 );
		add_action( 'styles_process',        array($this->wp_bridge, 'register_property'), 15, 1 );
		add_action( 'styles_process',        array($this->wp_bridge, 'process'), 20, 1 );
		
		add_action( 'styles_after_process',  array($this->properties, 'styles_post_process'), 10, 1 );
		add_action( 'styles_after_process',  array($this->wp_bridge, 'post_process'), 20, 1 );
		
		// Minify
		// $this->contents = Minify_CSS_Compressor::process($this->contents);
	}
	
	/**
	 * Finds a file relative to the source file from a URL
	 * @access public
	 * @param $url
	 * @return mixed
	 */
	public function find($url)
	{
		if($url[0] == '/' OR $url[0] == '\\')
		{
			$path = $_SERVER['DOCUMENT_ROOT'].$url;
			if ( !file_exists($path) ) {
				$path = false;
			}
		}
		else
		{
			$import_paths = $this->import_paths;
			array_unshift($import_paths, dirname($this->basepath));
			
			foreach ( $import_paths as $import_path ) {
				$path = $import_path.DIRECTORY_SEPARATOR.$url;
				if ( file_exists($path) ) {
					break;
				}
			}
			if ( !file_exists($path) ) {
				$path = false;
			}
		}
		
		return $path;
	}
	
}