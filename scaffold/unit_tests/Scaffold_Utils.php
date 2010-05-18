<?php

require_once('simpletest/autorun.php');
require_once('../scaffold/libraries/Scaffold/Utils.php');

class UtilityTests extends UnitTestCase
{
	
	function testUrlPath()
	{
		$this->assertEqual( Scaffold_Utils::url_path(dirname(__FILE__)), '/unit_tests');
		$this->assertEqual( Scaffold_Utils::url_path(dirname(__FILE__) . '/..'), '/');
		$this->assertEqual( Scaffold_Utils::url_path(dirname(__FILE__) . '/../scaffold/'), '/scaffold');
	}

	function testFixPath()
	{
		$path = dirname(__FILE__);
		$path = str_replace('/','\\',$path);
		$path = Scaffold_Utils::fix_path($path);
		$this->assertEqual($path,dirname(__FILE__) . '/');
	}
	
	function testPregQuote()
	{
		$string = "#selector-name.class   {background: url(_images/blah.png); }";
		$string = Scaffold_Utils::preg_quote($string);
		$this->assertEqual($string,'\#selector\-name\.class\s*\{background\:\s*url\(_images\/blah\.png\);\s*\}');
	}
	
	function testIsImage()
	{
		$this->assertTrue( Scaffold_Utils::is_image('path/to/file.png') );
		$this->assertTrue( Scaffold_Utils::is_image('path/to/file.jpg') );
		$this->assertTrue( Scaffold_Utils::is_image('path/to/file.jpeg') );
		$this->assertTrue( Scaffold_Utils::is_image('path/to/file.gif') );
		$this->assertFalse( Scaffold_Utils::is_image('path/to/file.css') );
	}
	
	function testIsCSS()
	{
		$this->assertFalse( Scaffold_Utils::is_css('path/to/file.png') );
		$this->assertFalse( Scaffold_Utils::is_css('path/to/file.jpg') );
		$this->assertFalse( Scaffold_Utils::is_css('path/to/file.jpeg') );
		$this->assertFalse( Scaffold_Utils::is_css('path/to/file.gif') );
		$this->assertTrue( Scaffold_Utils::is_css('path/to/file.css') );
	}
	
	function testMatch()
	{
		$string = "The quick brown fox jumps over the lazy dog.";
		$this->assertEqual(count(Scaffold_Utils::match('/The/',$string)),1);
		$this->assertEqual(count(Scaffold_Utils::match('/Lazy/',$string,1)),'L');
	}
	
	function testRemoveAllQuotes()
	{
		$string = "\"This \"magic\" isn't they're favourite thing\"";
		$this->assertEqual(Scaffold_Utils::remove_all_quotes($string), 'This magic isnt theyre favourite thing');
	}
	
	function testUnquote()
	{
		$string = "'http://awesome.com'";
		$this->assertEqual(Scaffold_Utils::unquote($string),'http://awesome.com');
	}
	
	function testRightSlash()
	{
		$this->assertEqual(Scaffold_Utils::right_slash('/This/is/my/awesome/path'), '/This/is/my/awesome/path/');
		$this->assertEqual(Scaffold_Utils::right_slash('/This/is/my/awesome/path///'), '/This/is/my/awesome/path/');
	}
	
	function testLeftSlash()
	{
		$this->assertEqual(Scaffold_Utils::left_slash('This/is/my/awesome/path'), '/This/is/my/awesome/path');
		$this->assertEqual(Scaffold_Utils::left_slash('///This/is/my/awesome/path/'), '/This/is/my/awesome/path/');
	}
	
	function testTrimSlashes()
	{
		$this->assertEqual(Scaffold_Utils::trim_slashes('///This/is/my/awesome/path'), 'This/is/my/awesome/path');
		$this->assertEqual(Scaffold_Utils::trim_slashes('This/is/my/awesome/path///'), 'This/is/my/awesome/path');
		$this->assertEqual(Scaffold_Utils::trim_slashes('///This/is/my/awesome/path///'), 'This/is/my/awesome/path');
	}
	
	function testReduceDoubleSlashes()
	{
		$this->assertEqual(Scaffold_Utils::reduce_double_slashes('///This/is/my/awesome/path'), '/This/is/my/awesome/path');
	}
	
	function testJoinPath()
	{
		$path1 = "/Blah/Bloo/Bleh///";
		$path2 = "foo/bar";
		$path3 = "bar//of/foos/";
		$this->assertEqual(Scaffold_Utils::join_path($path1,$path2,$path3), '/Blah/Bloo/Bleh/foo/bar/bar/of/foos/');
	}
	
	function testReadableSize()
	{
		$this->assertEqual(Scaffold_Utils::readable_size(1), '1 bytes');
	}
}