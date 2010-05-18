<?php

include '_load.php';

class ModulesTests extends UnitTestCase
{
	function dir($name = false)
	{
		static $directory;

		if($name)
		{
			$directory =  dirname(__FILE__) . '/_files/'.$name.'/';
			Scaffold::$current['path'] = $directory;
		}
		return $directory;
	}
	
	function load_files($name)
	{
		$this->dir($name);
		$list = array();
		$d = dir($this->dir());
		while (false !== ($entry = $d->read())) 
		{
		    if (preg_match('/_out\.css$/', $entry, $m) || $entry[0] == ".") 
		     	continue;
		     	
			$list[] = $entry;
		}
		$d->close();
		
		return $list;
	}

	function test_Absolute_Urls()
	{
		$this->dir('Absolute_Urls');
		$original = file_get_contents( $this->dir() . 'original.css');
		$expected = file_get_contents( $this->dir() . 'expected.css');
		$css = Absolute_Urls::formatting_process($original);
		$this->assertEqual($css,$expected);
	}

	function test_Constants()
	{
		$this->dir('Constants');
		$original = file_get_contents( $this->dir() . 'in.css');
		$expected = file_get_contents( $this->dir() . 'out.css');
		$css = Constants::pre_process($original);
		$css = Constants::replace($css);
		$this->assertEqual($css,$expected);
	}
	
	function test_Flags()
	{
		$this->dir('Flags');
		Scaffold::flag_set('flag2');
		Scaffold::flag_set('flag3');
		$original = file_get_contents( $this->dir() . 'in.css');
		$expected = file_get_contents( $this->dir() . 'out.css');
		$css = Flags::post_process($original);
		$this->assertEqual($css,$expected);
	}
	
	function test_Import()
	{
		$this->dir('Import');
		$original = file_get_contents( $this->dir() . 'in.css' );
		$expected = file_get_contents( $this->dir() . 'out.css' );
		Scaffold::$current['file'] = $this->dir() . 'in.css';
		Scaffold::add_include_path( $this->dir() );
		$css = Import::import_process($original);
		$this->assertEqual($expected,$css);
	}
	
	function test_Iteration()
	{
		$this->dir('Iteration');
		$original = file_get_contents( $this->dir() . 'in.css');
		$expected = file_get_contents( $this->dir() . 'out.css');
		$css = Iteration::parse($original);
		$this->assertEqual($css,$expected);
	}
	
	function test_Mixins()
	{
		foreach($this->load_files('Mixins') as $item)
		{ 
			$out = str_replace('.css','_out.css',$item);
			$original = file_get_contents($item);
			$expected = file_get_contents($out);
			
			$css = Mixins::parse($original);
			
			$css = Formatter::minify($css);
			$expected = Formatter::minify($expected);
			
			$this->assertEqual($expected,$css);
		}
	}
	/*
	function test_Nested_Selectors()
	{
		$this->dir('NestedSelectors');
		$original = file_get_contents( $this->dir() . 'in.css');
		$expected = file_get_contents( $this->dir() . 'out.css');
		$css = NestedSelectors::parse($original);
		$this->assertEqual($css,$expected);
	}
	*/

}

/*
// Selector Tests		
$files = array(
	'/unit_tests/_files/Misc/selectors.css'
);
$result = Scaffold::parse($files,$this->config,$options,true);
$this->assertFalse( $result['error'] );

// Hacks test
$files = array(
	'/unit_tests/_files/Misc/hacks.css'
);
$this->config['display_errors'] = true;
$result = Scaffold::parse($files,$this->config,$options,true);
$this->assertFalse( $result['error'] );

// General styles
$files = array(
	'/unit_tests/_files/Misc/styles.css'
);
$result = Scaffold::parse($files,$this->config,$options,true);
$this->assertFalse( $result['error'] );

// Unusual CSS strings
$files = array(
	'/unit_tests/_files/Misc/unusual_strings.css'
);
$result = Scaffold::parse($files,$this->config,$options,true);
$this->assertFalse( $result['error'] );

// Multiple Files
$files = array(
	'/unit_tests/_files/Misc/general.css',
	'/unit_tests/_files/Misc/minified.css',
	'/unit_tests/_files/Misc/selectors.css',
	'/unit_tests/_files/Misc/hacks.css',
	'/unit_tests/_files/Misc/styles.css',
	'/unit_tests/_files/Misc/unusual_strings.css'
);
$result = Scaffold::parse($files,$this->config,$options,true);
$this->assertFalse( $result['error'] );
*/