<?php

require_once('simpletest/autorun.php');
require_once('../scaffold/libraries/Scaffold/CSS.php');

class CSSUtilityTests extends UnitTestCase
{
	function testRemoveComments()
	{
		$this->assertEqual( Scaffold_CSS::remove_comments('/* Comment */'), '');
		$this->assertEqual( Scaffold_CSS::remove_comments('/* http://www.google.com */'), '');
		$this->assertEqual( Scaffold_CSS::remove_comments('/* /* */'), '');
		$this->assertEqual( Scaffold_CSS::remove_comments("/* \n\n\r\t */"), '');
	}
	
	function testRemoveProperties()
	{
		$this->assertEqual( Scaffold_CSS::remove_properties('border','.*?', '#id{border:1px;}'), '#id{}');
		$this->assertEqual( Scaffold_CSS::remove_properties('border','1px', '#id{border:1px;border:2px;}'), '#id{border:2px;}');
	}
	
	function testEncodeEntities()
	{
		$this->assertEqual( Scaffold_CSS::convert_entities('encode','"'),"#SCAFFOLD-QUOTE#");
		$this->assertEqual( Scaffold_CSS::convert_entities('encode','""'),"#SCAFFOLD-QUOTE##SCAFFOLD-QUOTE#");
		$this->assertEqual( Scaffold_CSS::convert_entities('encode','#id{background: url(data:image/png;base64,iVBORw0KGgoAA);}'),"#id{background: url(#SCAFFOLD-IMGDATA-PNG#base64,iVBORw0KGgoAA);}");
	}
	
	function testSelectorExists()
	{
		$this->assertTrue( Scaffold_CSS::selector_exists('#id','#id{}') );
		$this->assertFalse( Scaffold_CSS::selector_exists('#id2','#id{}') );
	}
	
	function testFindProperty()
	{
		$this->assertEqual( Scaffold_CSS::find_property('border','#id{border:1px;}'), array( array('border:1px;'), array('border'), array('1px')));
	}
}