<?php

include '_load.php';

class LogTests extends UnitTestCase
{
	function setup()
	{
		Scaffold_Log::enable(true);
		Scaffold_Log::setup( dirname(__FILE__) . '/../scaffold/logs' );
		Scaffold_Log::$log = array();
		return true;
	}

	function testSetup()
	{
		$this->assertTrue( $this->setup() );
	}
	
	function testEnabled()
	{
		Scaffold_Log::enable(true);
		$this->assertTrue( Scaffold_Log::$enabled );
	}

	function testLogDirectory()
	{
		$this->assertNotNull( Scaffold_Log::log_directory() );
	}
	
	function testDisabled()
	{
		$this->setup();
		Scaffold_Log::enable(false);
		$this->assertFalse( Scaffold_Log::$enabled );
		$this->assertFalse( Scaffold_Log::log('This shouldn\'t be logged') );
		$this->assertFalse( Scaffold_Log::save() );
		$this->assertFalse( Scaffold_Log::log_directory() );
	}
	
	function testLog()
	{
		$this->setup();
		Scaffold_Log::log('This should be logged');
		$this->assertEqual( count(Scaffold_Log::$log), 1);
	}
	
	function testSave()
	{
		$this->setup();
		Scaffold_Log::log('This should be logged');
		$this->assertTrue( Scaffold_Log::save() );	
	}
}