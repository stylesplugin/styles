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
require 'WordPressBridge.php';

class StormCSSParser {
	
	var $path;
	var $helper;
	var $contents;
	var $original;
	var $wp_bridge;
	var $import_paths;
	
	function __construct( $path, $styles ) {
		
		$this->import_paths = array(
			get_stylesheet_directory(),
			$styles->plugin_dir_path(),
		);
		
		$this->helper->css = new Scaffold_Helper_CSS();
		
		$this->path = $path;
		$this->contents = $this->original = file_get_contents( $path );
		
		$nested = new Scaffold_Extension_NestedSelectors();
		$nested->post_process( $this, null );
		
		$this->wp_bridge = new Scaffold_Extension_WordPressBridge();
		$properties = new Scaffold_Extension_Properties();
		
		$this->wp_bridge->initialize( $this, null );
		$this->wp_bridge->register_property( $properties );
		$properties->post_process( $this, $this );
		$this->wp_bridge->post_process( $this, null );
		
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