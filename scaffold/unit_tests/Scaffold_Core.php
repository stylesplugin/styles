<?php

include '_load.php';

class CoreTests extends UnitTestCase
{
	var $config;
	var $cache;

	function testSetFlag()
	{
		$this->assertTrue( Scaffold::flag_set('Flag') );
	}
	
	function setupCore()
	{
		if($this->config == null)
			$this->loadConfig();

		$this->assertTrue( Scaffold::setup($this->config) );

		foreach(Scaffold::modules() as $module)
		{
			$this->assertTrue( is_object($module) );
		}
		
		$this->assertTrue( is_array(Scaffold::include_paths()) );
		$this->assertTrue( is_array(Scaffold::modules()) );
		return true;
	}
	
	function loadConfig()
	{
		include '../scaffold/config.php';
		$config['system']  = realpath('../scaffold/') . '/';
		$config['cache']   = $config['system'] . 'cache/';
		$this->config = $config;
		return true;
	}
	
	function testLoadConfig()
	{
		$this->assertTrue( $this->loadConfig() );
	}
	
	function testSetupCore()
	{
		$this->assertTrue( $this->setupCore() );
	}
	
	function testSetupDevelopment()
	{
		$this->loadConfig();
		$this->config['in_production'] = false;
		$this->setupCore();
		$this->assertFalse( Scaffold::$config['in_production'] );
	}
	
	function testSetupProduction()
	{
		$this->loadConfig();
		$this->config['in_production'] = true;
		$this->setupCore();
		$this->assertTrue( Scaffold::$config['in_production'] );
	}

	function testSetupCache()
	{
		$this->loadConfig();
		
		$cache = new Scaffold_Cache
		(
			$this->config['cache'],
			$this->config['cache_lifetime'],
			$this->config['in_production'] 
		);
		
		$this->assertTrue( is_object($cache) );
	}
	
	// General stress-testing of the parsing function.
	function testParse()
	{
		$this->loadConfig();
		$this->config['display_errors'] = false;
		$this->setupCore();
		$options = array();

		// Single files
		$files = array('/unit_tests/_files/Misc/general.css');
		$result = Scaffold::parse($files,$this->config,$options,true);
		$this->assertFalse( $result['error'] );

		// Multiple Files
		$files = array(
			'/unit_tests/_files/Misc/general.css',
			'/unit_tests/_files/Misc/minified.css'
		);
		$result = Scaffold::parse($files,$this->config,$options,true);
		$this->assertFalse( $result['error'] );
		
		// Same file twice
		$files = array(
			'/unit_tests/_files/Misc/general.css',
			'/unit_tests/_files/Misc/general.css'
		);
		$result = Scaffold::parse($files,$this->config,$options,true);
		$this->assertFalse( $result['error'] );
	}
	
	function testErrors()
	{
		$this->loadConfig();
		$this->config['display_errors'] = false;
		$this->config['in_production'] = false;
		$this->setupCore();
		$options = array();

		// Via a url 
		$files = array(
			'http://scaffold/unit_tests/_files/Misc/general.css'
		);
		$result = Scaffold::parse($files,$this->config,$options,true);
		$this->assertTrue( $result['error'] );
	}
	
	function testReset()
	{
		$this->loadConfig();
		Scaffold::setup($this->config);
	}

}