<?php

include '_load.php';

class CacheTests extends UnitTestCase
{
	private $cache;

	function testCreateCache()
	{
		$path = dirname(__FILE__) . '/_files/cache/';
		$this->assertTrue( is_dir($path) );

		$this->cache = new Scaffold_Cache($path, 3600, false);
		$this->assertTrue( is_object($this->cache) );
		
		$anothercache = new Scaffold_Cache($path, 0, true);
		$this->assertTrue( is_object($anothercache) );
	}
	
	function testModified()
	{
		$this->assertTrue( is_int($this->cache->modified('test.css')) );
	}
	
	function testWrite()
	{
		$this->assertTrue( $this->cache->write('Test cache file','test.css') );
	}
	
	function testFreeze()
	{
		# Lock it
		$this->cache->freeze(true);
		$this->assertFalse( $this->cache->recache('test.css',0) );
		
		# Unlock it
		$this->cache->freeze(false);
		$this->assertTrue( $this->cache->recache('test.css',0) );
		
		# Lock it, but try it on a file that doesn't exist
		$this->cache->freeze(true);
		$this->assertTrue( $this->cache->recache('doesntexist.css',0) );
		
		# Lock it, but use a time behind the modified time of the file
		# This should return it as recache.
		$this->cache->freeze(true);
		$this->assertTrue( $this->cache->recache('doesntexist.css',-1) );
	}
	
	function testFind()
	{
		$this->assertEqual( $this->cache->find('test.css'), dirname(__FILE__).'/_files/cache/test.css' );
	}
	
	function testRemove()
	{
		$this->assertTrue( $this->cache->remove('test.css') );
	}
	
	function testCreate()
	{
		$this->cache->create('/foo/bar/');
		$this->assertTrue( is_dir( dirname(__FILE__).'/_files/cache/foo/bar' ) );
	}
	
	function testRemoveDir()
	{
		$this->assertTrue( $this->cache->remove_dir('foo') );
	}
	
	function testFetch()
	{
		$this->assertTrue( $this->cache->write('Test cache file','test.css') );
		$this->assertEqual($this->cache->fetch('test.css',0),'Test cache file');
		$this->assertNull($this->cache->fetch('test2.css',0));
	}
	
	function testOpen()
	{
		$this->assertEqual($this->cache->open('test.css'),'Test cache file');
		$this->assertFalse($this->cache->open('test2.css'));
	}
}